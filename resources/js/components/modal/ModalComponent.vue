<template>
    <div class="modal" :class='openmodal'>
        <div class="modal-background"></div>
        <div class="modal-card" :style = "options_modal">

            <header class="modal-card-head">
                <div class="modal-card-title">
                    <slot name="header">
                        <!-- Заголовок -->
                    </slot>
                </div>
                <button class="modal-close" aria-label="close" @click.prevent='close'></button>
            </header>
            <section class="modal-card-body">
                <slot name="body">
                    <!-- Сообщение -->
                </slot>
            </section>
            <footer class="modal-card-foot">
                <slot name="footer">
                    <!-- Кнопки -->
                    <!--<button class="button is-success" @click='save'>Сохранить</button>-->
                    <!--<button class="button" @click='close'>Отменить</button>-->
                </slot>
            </footer>
        </div>
    </div>
</template>

<script>
    export default{
        props:['openmodal', 'options'],
        computed: {
            options_modal() {
                let width = '640px';
                let height = '';
                if (this.options) {
                    if (this.options.width !== undefined) {
                        width = this.options.width + 'px';
                    }
                    if (this.options.height !== undefined) {
                        height = this.options.height + 'px';
                    }
                }
                return {
                    width,
                    height
                }
            }
        },
        data() {
            return {
                errors: {}
            }
        },
        methods:{
            close() {
                this.$emit('closeRequest')
            },
        }
    }
</script>
