<template>
    <div class="catalog">
            <section>
                <b-row>
                    <b-col>
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
                    </b-col>
                    <b-col>
                        <b-button href="/basket" variant="success">Перейти в корзину</b-button>
                    </b-col>
                </b-row>
            </section>
            <br />
            <b-table
                :busy="isBusy"
                :filter="filter"
                :filterIncludedFields="filterOn"
                @filtered="onFiltered"
                bordered
                :sticky-header="stickyHeader"
                sticky-header="860"
                striped
                hover
                :items="products"
                :fields="fieldsProducts"
            >
                <template v-slot:table-busy>
                    <div class="text-center text-success my-2">
                        <b-spinner variant="success" label="Spinning" />
                        <strong>Загрузка...</strong>
                    </div>
                </template>
                <template v-slot:cell(name)="data">
                    <a :href="'/catalog/detail/' + data.item.id">{{data.item.name}}</a>
                </template>
                <template v-slot:cell(action)="data">
                    <template v-if="data.item.quantity === 0">
                        <b-button v-if="Number(data.item.type_id) === 1" disabled variant="success">Собрать</b-button>
                        <b-button v-if="Number(data.item.type_id) === 0" disabled variant="success">Добавить</b-button>
                    </template>
                    <template v-else>
                        <b-button v-if="Number(data.item.type_id) === 1" @click.default="collectProduct(data)" variant="success">Собрать</b-button>
                        <b-button v-if="Number(data.item.type_id) === 0" @click.default="addProduct(data)" variant="success">Добавить</b-button>
                    </template>
                </template>
                <template v-slot:cell(count)="data">
                    <b-form-spinbutton id="demo-sb" v-model="data.item.quantity" min="0" max="100"></b-form-spinbutton>
                </template>
            </b-table>
        <div>
            <catalog-complex-product-modal
                :modal="modal"
                :product="selectedProduct"
            />
        </div>
    </div>
</template>

<script>
    import modal from './../modal/ModalComponent';
    import CatalogComplexProductModal from './../modal/CatalogComplexProductModal';
    import axios from 'axios';

    export default {
        components: {
            modal,
            CatalogComplexProductModal,
        },
        methods: {
            notification() {
                this.$notify({
                    group: 'app',
                    title: 'Товар успешно добавлен в корзину',
                });
            },
            open() {
                this.modal.addActive = 'is-active';
            },
            collectProduct({item}) {
                this.selectedProduct = item;
                this.open();
            },
            addProduct({item}) {
                axios.post('/api/basket', {id: item.id, quantity: item.quantity})
                    .then(response => {
                        this.notification();
                        item.quantity = 0;
                    })
                    .catch(e => {
                        console.log(e);
                    })
            },
        },
        created() {
            axios.get('/api/products')
                .then(response => {
                    const {data: {data}} = response;
                    this.products = data;
                    this.isBusy = false;
                })
                .catch(e => {
                    this.errors.push(e)
                });
        },
        data() {
            return {
                filter: null,
                filterOn: [],
                isBusy: true,
                stickyHeader: true,
                products: [],
                selectedProduct: {},
                modal: {
                    message: '',
                    addActive : '',
                    options: {
                        width: 1500,
                        height: 927,
                    }
                },
                fieldsProducts: [
                    {
                        key: 'number',
                        label: 'Артикул',
                        sortable: true
                    },
                    {
                        key: 'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key: 'header',
                        label: 'Расшифровка',
                        sortable: true
                    },
                    {
                        key: 'price',
                        label: 'Цена',
                        sortable: true
                    },
                    {
                        key: 'count',
                        label: 'Количество',
                    },
                    {
                        key: 'action',
                        label: 'Действие',
                        sortable: false,
                    },
                ],
            }
        }
    };
</script>

<style scoped>
    .catalog tr a {
        font-size: 16px;
    }

    .header {
        font-size: 18px;
        padding: 10px 0;
        color: #6574cd;
    }
</style>
