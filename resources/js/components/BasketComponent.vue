<template>
    <div>
        <div class="head">Корзина выбранных товаров</div>

        <template v-if="params.products && params.products.length > 0">
            <b-table
                :sticky-header="stickyHeader"
                sticky-header="620"
                bordered
                striped
                hover
                :items="params.products"
                :fields="fields"
            >
                <template v-slot:cell(action)="data">
                    <b-button v-if="data.item.options.set" size="sm" class="mb-2" variant="success" @click="showDetail(data.item)">
                        Содержание
                    </b-button>

                    <b-button size="sm" class="mb-2" variant="danger" @click="remove(data.item.rowId)">
                        <b-icon icon="trash"  />
                    </b-button>
                </template>
                <template v-slot:cell(article)="data">
                    {{data.item.id}}
                </template>
                <template v-slot:cell(summ)="data">
                    {{data.item.qty * data.item.price}}
                </template>
            </b-table>

            <div class="total-content">
                <span>Общая сумма заказа составляет <b-badge variant="secondary">   {{ params.total }} руб. </b-badge></span>
                <div>Ваша скидка {{ (parseFloat(params.total) * 0.10).toFixed(2) }}  руб.</div>
                <div>Итого к оплате {{ (parseFloat(params.total) - parseFloat(params.total) * 0.10).toFixed(2) }} руб.</div>
            </div>

            <b-link class="btn btn-success" href="/catalog">Изменить заказ</b-link>
            <b-link class="btn btn-success" @click="removeAll">Очистить корзину</b-link>
            <br />
            <br />
        </template>
        <template v-else>
            <div>
                <b-alert show variant="secondary">
                    <h4 class="alert-heading">Ваша корзина пуста</h4>
                    <hr>
                    <p>
                        Добавьте товары в корзину для этого перейдите в <strong><a href="/catalog">каталог</a></strong>
                    </p>
                </b-alert>
            </div>
        </template>

        <modal-component v-if="(detail !== null)" :openmodal='modal.addActive' @closeRequest='close' >
            <div slot="header">
                Содержание набора: {{detail.product.name}}
            </div>
            <div slot="body">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Артикул</th>
                            <th>Название</th>
                            <th>Количество</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template v-for="item in detail.allergens">
                            <tr>
                                <td> {{ item.code }} </td>
                                <td> {{ item.name }} </td>
                                <td> {{ detail.set[item.id] }} </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <div slot="footer">
                <b-button variant="outline-primary" @click='close'>Отменить</b-button>
            </div>
        </modal-component>

    </div>
</template>

<script>
    import ModalComponent from './modal/ModalComponent';
    import axios from 'axios';
    import { bus } from '../bus.js';

    export default {
        props: ['clickedNext', 'currentStep', 'params'],
        components: {
            ModalComponent,
        },
        mounted() {
            this.$emit('can-continue', {value: true});
        },
        watch: {
            clickedNext(val) {
                if(val === true) {
                    // this.$v.form.$touch();
                }
            }
        },
        methods: {
            close() {
                this.modal.addActive = '';
            },
            open() {
                this.modal.addActive = 'is-active';
            },
            showDetail(item) {
                axios.get(`/api/basket/${item.rowId}`)
                    .then(response => {
                        this.open();
                        this.detail = response.data;
                    })
                    .catch(e => {
                        this.errors.push(e)
                    });
            },
            getProductId(id) {
                let end = (id.indexOf("_"));
                return id.substr(0, end);
            },
            remove(rowId) {
                this.$dialog
                    .confirm({
                        title: 'Удаление товара.',
                        body: 'Вы уверены, что хотите удалить товар?'
                    }, {
                        title: "Удаление товара",
                        okText: 'Ок',
                        cancelText: 'Отмена',
                    })
                    .then((dialog) => {
                       bus.$emit('remove', rowId);
                    })
            },
            removeAll() {
                this.$dialog
                    .confirm({
                        title: 'Очистка корзины.',
                        body: 'Вы уверены, что хотите удалить товар?'
                    },
                    {
                        title: "Очистка корзины",
                        okText: 'Ок',
                        cancelText: 'Отмена'
                    })
                    .then((dialog) => {
                        bus.$emit('remove_all');
                    })
            },
        },
        data() {
            return {
                modal: {
                    addActive : '',
                },
                detail: null,
                stickyHeader: true,
                fields: [
                    {
                        key: 'article',
                        label: 'Артикул',
                        sortable: true
                    },
                    {
                        key: 'name',
                        label: 'Название',
                        sortable: true
                    },
                    {
                        key: 'price',
                        label: 'Цена',
                        sortable: true
                    },
                    {
                        key: 'qty',
                        label: 'Количество',
                        sortable: true
                    },
                    {
                        key: 'summ',
                        label: 'Сумма',
                        sortable: true
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
    .total-content {
        font-size: 20px;
        padding: 10px 0;
        float: right;
        font-weight: bold;
    }
</style>

