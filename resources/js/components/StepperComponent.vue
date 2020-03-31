<template>
    <section class="section">
        <div class="stepper_container">
            <div class="columns">
                <div class="column is-8 is-offset-2">
                    <horizontal-stepper v-if="this.isLoad === false"
                        locale="ru"
                        :params="params"
                        :steps="steps"
                        :keepAlive="keepAlive"
                        @completed-step="completeStep"
                        @active-step="isStepActive"
                        @stepper-finished="alert"
                    >
                    </horizontal-stepper>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
    import HorizontalStepper from './stepper';
    import StepOne from './BasketComponent.vue';
    import StepTwo from './FormComponent.vue';
    import StepOrder from './FormOrderComponent';
    import axios from "axios";
    import { bus } from '../bus.js';
    import { auth } from '../auth.js';

    export default {
        components: {
            HorizontalStepper
        },
        created() {
            this.init();
            /* удалить элемент */
            bus.$on('remove', this.remove);
            /* создан заказ */
            bus.$on('create_order', this.create_order);
        },
        methods: {
            create_order(order) {
                this.params = {
                    ...this.params,
                    order,
                };

                if (order) {
                    this.removeAll();
                }
            },
            init() {
                this.isLoad = true;
                axios.get('/api/basket')
                    .then(response => {
                        const {products, format_total} = response.data;
                        this.isLoad = false;
                        this.total = format_total;
                        /* передаем данные на 2 шаг, чтобы там их вывести */
                        this.params.products = products;
                        this.params.total = this.total;
                        auth.setUser(response.data.auth);
                    })
                    .catch(e => {
                        this.errors.push(e)
                    });
            },
            remove(rowId) {
                axios.delete(`/api/basket/${rowId}`)
                    .then(response => {
                        const products = this.params.products.filter((item) => {
                            if (item.rowId === rowId) {
                                this.total = this.total - item.price;
                                return false;
                            } else {
                                return item.rowId !== rowId;
                            }
                        });

                        this.params = {
                            ...this.params,
                            products,
                            total: this.total,
                        };
                    })
                    .catch(e => {
                        this.errors.push(e)
                    });
            },
            removeAll() {
                axios.delete('/api/basket/clear')
                    .then(response => {
                        this.params = {
                            ...this.params,
                            products: [],
                            total: 0,
                        };
                    })
                    .catch(e => {
                        this.errors.push(e)
                    });
            },
            // Executed when @completed-step event is triggered
            completeStep(payload) {
                this.steps.forEach((step) => {
                    if (step.name === payload.name) {
                        step.completed = true;
                    }
                })
            },
            // Executed when @active-step event is triggered
            isStepActive(payload) {
                this.steps.forEach((step) => {
                    if (step.name === payload.name) {
                        if(step.completed === true) {
                            step.completed = false;
                        }
                    }
                })
            },
            nextStep() {
                return true;
            },
            // Executed when @stepper-finished event is triggered
            alert(payload) {
                // finish
            }
        },
        data() {
            return {
                auth: {},
                is_auth: false,
                isLoad: false,
                keepAlive: false,
                params: {},
                // иконки https://materializecss.com/icons.html  done_all
                steps: [
                    {
                        icon: 'shopping_cart',
                        name: 'first',
                        title: 'Корзина',
                        component: StepOne,
                        completed: false,
                        button: 'Далее',

                    },
                    {
                        icon: 'local_shipping',
                        name: 'second',
                        title: 'Данные заказчика',
                        component: StepTwo,
                        completed: false,
                        button: 'Оформить заказ',
                    },
                    {
                        icon: 'mail',
                        name: 'order',
                        title: 'Заказ сформирован',
                        component: StepOrder,
                        completed: false,
                    },
                ]
            }
        },
    }
</script>

<style>
    .stepper-box .content {
        padding: 16px;
    }
</style>
