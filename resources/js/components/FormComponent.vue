<template>
    <div>

        <div class="head"> Контактные данные Заказчика </div>

        <b-form @submit="onSubmit" @reset="onReset">

            <b-alert show variant="primary" show>
                Проверьте данные, в случае необходимости внесите изменения.
            </b-alert>

            <b-form-group
                id="input-group-organization"
                label="Название организации"
                label-for="input-company"
            >
                <b-form-input
                    id="input-organization"
                    v-model="form.organization"
                    type="text"
                    placeholder="Введите название организации..."
                ></b-form-input>
            </b-form-group>

            <b-form-group
                id="input-group-address"
                label="Адрес доставки"
                label-for="input-address"
            >
                <b-form-input
                    id="input-address"
                    v-model="form.address"
                    type="text"
                    required
                    placeholder="Введите адрес доставки..."
                ></b-form-input>
            </b-form-group>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-name"
                        label="Контактное лицо *"
                        label-for="input-name"
                    >
                        <b-form-input
                            id="input-name"
                            v-model="form.name"
                            type="text"
                            required
                            placeholder="Введите ФИО..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group
                        id="input-group-post"
                        label="Должность"
                        label-for="input-post"
                    >
                        <b-form-input
                            id="input-post"
                            v-model="form.post"
                            type="text"
                            placeholder="Введите должность..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-email"
                        label="Email *"
                        label-for="input-email"
                    >
                        <b-form-input
                            id="input-email"
                            v-model="form.email"
                            type="text"
                            required
                            placeholder="Введите email..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group
                        id="input-group-phone"
                        label="Контактный телефон *"
                        label-for="input-phone"
                    >
                        <b-form-input
                            id="input-phone"
                            v-model="form.phone"
                            type="text"
                            required
                            placeholder="Введите телефон..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-legal_address"
                        label="Юридический адрес"
                        label-for="input-inn"
                    >
                        <b-form-input
                            id="input-legal_address"
                            v-model="form.legal_address"
                            type="text"
                            placeholder="Введите юридический адрес..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-ogrn"
                        label="ОГРН"
                        label-for="input-ogrn"
                    >
                        <b-form-input
                            id="input-ogrn"
                            v-model="form.ogrn"
                            type="text"
                            placeholder="Введите ОГРН..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group
                        id="input-group-okpo"
                        label="ОКПО"
                        label-for="input-okpo"
                    >
                        <b-form-input
                            id="input-okpo"
                            v-model="form.okpo"
                            type="text"
                            placeholder="Введите ОКПО..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-inn"
                        label="ИНН"
                        label-for="input-inn"
                    >
                        <b-form-input
                            id="input-inn"
                            v-model="form.inn"
                            type="text"
                            placeholder="Введите ИНН..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group
                        id="input-group-kpp"
                        label="КПП"
                        label-for="input-kpp"
                    >
                        <b-form-input
                            id="input-kpp"
                            v-model="form.kpp"
                            type="text"
                            placeholder="Введите КПП..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-rs"
                        label="Расчетный счет"
                        label-for="input-rs"
                    >
                        <b-form-input
                            id="input-rs"
                            v-model="form.rs"
                            type="text"
                            placeholder="Введите Расчетный счет..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group
                        id="input-group-ks"
                        label="Кор. счет"
                        label-for="input-ks"
                    >
                        <b-form-input
                            id="input-ks"
                            v-model="form.ks"
                            type="text"
                            placeholder="Введите Кор. счет..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-row>
                <b-col>
                    <b-form-group
                        id="input-group-bank"
                        label="Банк"
                        label-for="input-bank"
                    >
                        <b-form-input
                            id="input-bank"
                            v-model="form.bank"
                            type="text"
                            placeholder="Введите Банк..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
                <b-col>
                    <b-form-group
                        id="input-group-bik"
                        label="Бик"
                        label-for="input-bik"
                    >
                        <b-form-input
                            id="input-bik"
                            v-model="form.bik"
                            type="text"
                            placeholder="Введите Бик..."
                        ></b-form-input>
                    </b-form-group>
                </b-col>
            </b-row>

            <b-form-textarea
                id="textarea"
                v-model="form.notes"
                placeholder="Примечание и реквизиты..."
                rows="3"
                max-rows="6"
            ></b-form-textarea>

            <br />

            <b-button class="btn btn-success" @click="open" >Проверить заказ</b-button>
            <b-button type="reset" variant="outline-primary">Очистить форму</b-button>
            <b-button type="submit"  class="btn btn-success">Оформить заказ</b-button>
        </b-form>

        <modal-component :openmodal='modal.addActive' :options="modal.options"  @closeRequest='close'>

            <div slot="header">
                Проверка заказа
            </div>
            <div slot="body">

                <h4> Данные заказчика </h4>

                <div slot="header">

                    <table class="table table-striped">
                        <tr>
                            <td>Название Организации</td>
                            <td> {{ form.organization || '-' }}</td>
                        </tr>
                        <tr>
                            <td>Адрес доставки</td>
                            <td> {{ form.address || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Контактное лицо</td>
                            <td> {{ form.name || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Должность </td>
                            <td> {{ form.post || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Email </td>
                            <td> {{ form.email || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Телефон </td>
                            <td> {{ form.phone || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Сопроводительный текст </td>
                            <td> {{ form.rs || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Юридический адрес </td>
                            <td> {{ form.legal_address || '-' }} </td>
                        </tr>
                        <tr>
                            <td> ОГРН </td>
                            <td> {{ form.ogrn || '-' }} </td>
                        </tr>
                        <tr>
                            <td> ОКПО </td>
                            <td> {{ form.okpo || '-' }} </td>
                        </tr>
                        <tr>
                            <td> ИНН </td>
                            <td> {{ form.inn || '-' }} </td>
                        </tr>
                        <tr>
                            <td> КПП </td>
                            <td> {{ form.kpp || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Расчетный счет </td>
                            <td> {{ form.rs || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Кор. счет </td>
                            <td> {{ form.ks || '-' }} </td>
                        </tr>
                        <tr>
                            <td> Банк </td>
                            <td> {{ form.bank || '-' }} </td>
                        </tr>
                        <tr>
                            <td> БИК </td>
                            <td> {{ form.bik || '-' }} </td>
                        </tr>
                    </table>
                </div>

                <h4> Содержание заказа </h4>

                <b-table
                    sticky-header="620"
                    bordered
                    striped
                    hover
                    :items="params.products"
                    :fields="fields"
                >
                    <template v-slot:cell(summ)="data">
                        {{data.item.qty * data.item.price}}
                    </template>
                    <template v-slot:cell(number)="data">
                        {{data.item.options.number}}
                    </template>
                </b-table>
            </div>
            <div slot="footer">
                <b-button variant="outline-primary" @click='close'>Закрыть</b-button>
            </div>
        </modal-component>
    </div>
</template>

<script>
    import axios from 'axios';
    import ModalComponent from './modal/ModalComponent';
    import { bus } from '../bus.js';
    import { auth } from '../auth.js';

    export default {
        components: {
            ModalComponent,
        },
        props: ['clickedNext', 'currentStep', 'params'],
        mounted() {
            this.$emit('can-continue', {value: true});
            if (auth.getUser() !== null) {
                const user = auth.getUser();
                this.form = {
                    ...this.form,
                    name: user.name || '',
                    email: user.email,
                    phone: user.phone || '',
                    post: user.post || '',
                    address: user.address || '',
                    organization: user.organization || '',
                    inn: user.inn || '',
                    kpp: user.kpp || '',
                    rs: user.rs || '',
                    ks: user.ks || '',
                    bank: user.bank || '',
                    bik: user.bik || '',
                    okpo: user.okpo || '',
                    ogrn: user.ogrn || '',
                    legal_address: user.legal_address || '',
                }
            }
        },
        data() {
            return {
                order: {},
                user: {},
                fields: [
                    {
                        key: 'number',
                        label: 'Артикул',
                    },
                    {
                        key: 'name',
                        label: 'Название',
                    },
                    {
                        key: 'price',
                        label: 'Цена',
                    },
                    {
                        key: 'qty',
                        label: 'Количество',
                    },
                    {
                        key: 'summ',
                        label: 'Сумма',
                    },
                ],
                modal: {
                    addActive : '',
                    options: {
                        width: 1500,
                    }
                },
                form: {
                    name: '',
                    email: 'test@gmail.com',
                    phone: '',
                    post: '',
                    address: '',
                    organization: '',
                    inn: '',
                    kpp: '',
                    rs: '',
                    ks: '',
                    bank: '',
                    bik: '',
                    okpo: '',
                    ogrn: '',
                    legal_address: '',
                    notes: '',
                },
            }
        },
        methods: {
            open() {
                this.modal.addActive = 'is-active';
            },
            close() {
                this.modal.addActive = '';
            },
            init() {
                this.form = {
                    name: '',
                    email: '',
                    phone: '',
                    post: '',
                    address: '',
                    organization: '',
                    inn: '',
                    kpp: '',
                    notes: '',
                    rs: '',
                    ks: '',
                    bank: '',
                    bik: '',
                    okpo: '',
                    ogrn: '',
                    legal_address: '',
                };
            },
            onSubmit(evt) {
                evt.preventDefault();
                axios.post('/api/request', this.form)
                    .then(response => {
                        // очистить поля в форме
                        this.init();
                        // очистить корзину
                        bus.$emit('create_order', response.data);
                    })
                    .catch(e => {
                        this.errors.push(e)
                    })
            },
            onReset(evt) {
                evt.preventDefault();
                this.init();
            }
        }
    }
</script>
