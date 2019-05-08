<template>
    <div class="card">
        <div class="card-header">Console</div>
        <div class="card-body">
            <div id="terminal"></div>
        </div>
        <div class="card-footer">
            <input
                type="text"
                class="form-control console-input"
                placeholder="Send command"
                v-model="input"
                :disabled="!isRunning"
                @keyup.enter="submit"
            >
        </div>
    </div>
</template>

<script>
    import Ttyd from '@/utils/Ttyd';

    export default {
        name: 'ServerConsole',

        props: {
            isRunning: {
                type: Boolean,
                default: false
            }
        },

        data() {
            return {
                input: '',          // Console command input.
            };
        },

        mounted() {
            if (this.isRunning) {
                this.ttyd = new Ttyd('ws://localhost:7681/ws');
            }
        },

        methods: {
            /**
             * Submits a command to be sent to the server.
             *
             * @return {Void}
             */
            submit() {
                axios.post('/api/server/command', {
                    cmd: this.input
                })
                .then(response => {
                    this.input = '';
                })
                .catch(console.log);
            }
        },

        watch: {
            isRunning (isRunning, wasRunning) {
                if (!wasRunning) {
                    this.ttyd = new Ttyd('ws://localhost:7681/ws');
                }
            }
        }
    };
</script>

<style scoped>
    #terminal {
        min-height: 300px;
    }
</style>
