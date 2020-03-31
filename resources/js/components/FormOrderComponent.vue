<template>
    <div class="content-text">
       <template v-if="params.order">

           <div class="thank"> Спасибо! </div>
           <div class="order_content">
               Ваш заказ <strong>№ {{params.order.id}}</strong> успешно сформирован и выслан на указанный вами
               email-адрес <a :href="params.order.email">{{params.order.email}}</a>.
               <br />

               Проверить статус заказа Вы можете по <a :href="link">ссылке</a>.
           </div>
       </template>
        <template v-else>
            Заказ не создан
        </template>
    </div>
</template>

<script>
export default {
    props: ['clickedNext', 'currentStep', 'params'],
    mounted() {
        this.$emit('can-continue', {value: true});
    },
    watch: {
        clickedNext(val) {
        }
    },
    computed: {
        link() {
            return `/orders/${this.params.order.id}`;
        }
    }
}
</script>

<style>
    .order_content {
        padding: 60px 50px;
        text-align: center;
        line-height: 2;
        font-size: 18px;
    }

    .thank {
        font-size: 26px;
        text-align: center;
    }

    .content-text {
        font-size: 20px;
        padding: 60px 40px 40px 40px;
        margin: 100px 30px 30px 30px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        border-radius: 5px;
        color: black;
    }

    .content-text div {
        padding-top: 30px;
    }

</style>
