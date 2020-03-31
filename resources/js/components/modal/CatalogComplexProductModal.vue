<template>
    <modal-component :options='modal.options' :openmodal='modal.addActive' @closeRequest='close' >
        <div slot="header">
            Содержание набора: <i>{{this.product.number}}</i> - {{this.product.name}}
        </div>
        <div slot="body">
            <b-row>
                <b-col xl="7" md="7" lg="7">
                    <div class="header-content"> Список аллергенов для выбора </div>

                    <b-input-group>
                        <b-form-input
                            v-model="filter"
                            type="search"
                            id="filterInput"
                            placeholder="Поиск..."
                        ></b-form-input>
                        <b-input-group-append>
                            <b-button :disabled="!filter" @click="filter = ''">Очистить</b-button>
                        </b-input-group-append>
                    </b-input-group>

                    <br />

                    <b-table
                        :filter="filter"
                        :filterIncludedFields="filterOn"
                        @filtered="onFiltered"
                        :busy="isBusy"
                        :sticky-header="stickyHeader"
                        sticky-header="620"
                        bordered
                        striped
                        hover
                        :items="allergens"
                        :fields="fields"
                        outlined
                    >
                        <template v-slot:table-busy>
                            <div class="text-center text-success my-2">
                                <b-spinner variant="success" label="Spinning" />
                                <strong>Загрузка аллергенов...</strong>
                            </div>
                        </template>
                        <template v-slot:cell(name)="data">
                            <strong>{{data.item.name}}</strong>
                            <div>{{ getComposition(data.item.composition) }} </div>
                        </template>
                        <template v-slot:cell(category_id)="data">
                            <template v-if="data.item.category">
                                {{data.item.category.name }}
                            </template>
                            <template v-else>
                                -
                            </template>
                        </template>
                        <template v-slot:cell(type_id)="data">
                            <template v-if="data.item.type">
                                {{data.item.type.name }}
                            </template>
                            <template v-else>
                                -
                            </template>
                        </template>
                        <template v-slot:cell(action)="data">
                            <b-button v-if="data.item.quantity === 0" disabled variant="success">Добавить</b-button>
                            <b-button v-else="data.item.quantity === 0" @click.default="addAllergen(data)" variant="success">Добавить</b-button>
                        </template>
                        <template v-slot:cell(count)="data">
                            <b-form-spinbutton id="demo-sb" v-model="data.item.quantity" min="0" max="100"></b-form-spinbutton>
                        </template>
                    </b-table>
                </b-col>
                <b-col xl="5" md="5" lg="5">
                    <div class="header-content">
                        Всего определений <b-badge variant="primary"> {{selectedQuantity}}</b-badge>
                    </div>

                    <div v-if="selectedAllergensCount===0">
                        <b-alert show center variant="danger">Список пуст </b-alert>
                    </div>
                    <div v-else>
                        <div class="header-content">
                            Выбранные аллергены: <b-badge variant="primary">{{ selectedAllergensCount }}</b-badge> -
                            <b-badge variant="primary">{{selectedAllergenQuantity}}</b-badge> определений.
                            <b-badge v-if="selectedAllergenQuantity > selectedQuantity" variant="danger">Ошибка. Уменьшите количество алергенов</b-badge>
                        </div>

                        <b-table
                            :sticky-header="stickyHeader"
                            sticky-header="620"
                            bordered
                            striped
                            hover
                            :items="selectedAllergens"
                            :fields="selectedAllergensFields"
                            outlined
                        >
                            <template v-slot:cell(name)="data">
                                <strong>{{data.item.name}}</strong>
                            </template>
                            <template v-slot:cell(quantity)="data">
                                <b-form-spinbutton
                                    @input="(value) => changeQuantity(value, data.item)"
                                    :value="getValue(data.item.id)"
                                    id="demo-sb"
                                    min="1"
                                    max="100">
                                </b-form-spinbutton>
                            </template>
                            <template v-slot:cell(action)="data">
                                <b-button variant="danger" @click="removeSelectedAllergen(data.item)" >
                                    <b-icon icon="trash" />
                                </b-button>
                            </template>
                        </b-table>
                    </div>
                </b-col>
            </b-row>
        </div>
        <div slot="footer">
            <template v-if="selectedAllergenQuantity !== selectedQuantity">
                <b-button disabled class="disabled" variant="success" @click.default="addProduct({item: {id: 0}})">Добавить</b-button>
            </template>
            <template v-else>
                <b-button variant="success" @click.default="addProduct()" >Добавить</b-button>
            </template>
            <b-button variant="outline-primary" @click='close'>Отменить</b-button>
        </div>
    </modal-component>
</template>

<script>
    import ModalComponent from './../modal/ModalComponent';
    import axios from 'axios';

    export default {
        props: ['modal', 'product'],
        components: {
            ModalComponent,
        },
        computed: {
            selectedQuantity() {
                return Number(this.product.quantity) * Number(this.product.definitions_number);
            },
            selectedAllergenQuantity() {
                let total = 0;
                this.selectedAllergens.forEach(element => {
                    total += element.quantity * 8;
                });
                return total;
            },
        },
        beforeUpdate() {
            if (this.modal.addActive === '') {
                return;
            }

            if (this.allergens.length === 0) {
                this.getAllergens();
            }
        },

        methods: {
            getValue(id) {
                const arr = this.selectedAllergens.filter(item => item.id === id);
                if (arr.length > 0) {
                    return arr[0].quantity;
                }

                return null;
            },
            changeQuantity(value, item) {
                this.selectedAllergens = this.selectedAllergens.map(_item => {
                    return _item;
                });

            },
            removeSelectedAllergen({id}) {
                this.selectedAllergens = this.selectedAllergens.filter(item => item.id !== id);
                this.selectedAllergensCount--;
            },
            addAllergen({item}) {
                // ищем дубли
                const elements = this.selectedAllergens.filter(_item => _item.id === item.id);
                if (elements.length) {
                    this.selectedAllergens = this.selectedAllergens.map(_item => {
                       if (_item.id === item.id) {
                           _item.quantity += item.quantity;
                       }
                       return _item;
                    });
                } else {
                    const copyItem = {...item};
                    this.selectedAllergens.push(copyItem);
                    this.selectedAllergensCount++;
                }
                item.quantity = 0;
            },
            getComposition(composition) {
                if (composition === null) {
                    return;
                }

                return JSON.parse(composition).join(', ')
            },
            addProduct() {
                const set = [];
                this.selectedAllergens.forEach(element => {
                    set.push({id: element.id, quantity: element.quantity});
                });

                axios.post('/api/basket', {id: this.product.id, quantity: this.product.quantity, set})
                    .then(response => {
                        this.notification();
                        this.close();
                        this.product.quantity = 0;
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
            onFiltered(filteredItems) { },
            close() {
                this.modal.addActive = '';
            },
            notification() {
                this.$notify({
                    group: 'app',
                    title: 'Товар успешно добавлен в корзину',
                });
            },
            getAllergens() {
                axios.get('/api/allergens')
                    .then(response => {
                        const {data: {data}} = response;
                        this.allergens = data;
                        this.isBusy = false;
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
        },
        data() {
            return {
                filter: null,
                filterOn: [],
                isBusy: true,
                stickyHeader: true,
                allergens: [],
                /* списбок набранных алергенов */
                selectedAllergensCount: 0,
                selectedAllergens: [],
                fields: [
                    {
                        key: 'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key: 'category_id',
                        label: 'Категория',
                        sortable: true
                    },
                    {
                        key: 'type_id',
                        label: 'Тип',
                        sortable: true,
                    },
                    {
                        key: 'count',
                        label: 'Количество x 8',
                    },
                    {
                        key: 'action',
                        label: 'Действие',
                        sortable: false,
                    },
                ],
                selectedAllergensFields: [
                    {
                        key: 'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key: 'quantity',
                        label: 'Количество x 8',
                        sortable: false
                    },
                    {
                        key: 'action',
                        label: 'Действие',
                        sortable: false
                    },
                ],
            }
        }
    };
</script>

<style scoped>
    .header-content {
        font-size: 18px;
        padding: 10px 0px;
    }
</style>
