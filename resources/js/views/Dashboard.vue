<template>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-5">
                <div class="card mb-4">
                    <div class="card-header">Server Controls</div>
                    <div class="card-body">
                        <div class="btn-group">
                            <button @click.prevent="serverStart" class="btn btn-lg btn-primary" title="Start" :disabled="isRunning === true">Start</button>
                            <button @click.prevent="serverStop" class="btn btn-lg btn-danger" title="Stop" :disabled="isRunning === false">Stop</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7">
                <server-console :is-running="isRunning" />
            </div>
        </div>
    </div>
</template>

<script>
    import ServerConsole from '@/components/ServerConsole';

    export default {
        name: 'Dashboard',

        components: {
            ServerConsole
        },

        data() {
            return {
                isRunning: false,
            }
        },

        mounted() {
            this.serverRunning();
        },

        methods: {
            /**
             * Check if the server is online or not.
             *
             * @return {Void}
             */
            serverRunning() {
                axios.get('/api/server/running')
                .then(response => {
                    this.isRunning = response.data;

                    window.setTimeout(this.serverRunning, 1000);
                })
                .catch(console.log);
            },

            /**
             * Start the Minecraft server.
             *
             * @return {Void}
             */
            serverStart() {
                axios.post('/api/server', {
                    action: 'start'
                })
                .then(response => {
                    this.isRunning = true;
                })
                .catch(console.log);
            },

            /**
             * Stop the Minecraft server.
             *
             * @return {Void}
             */
            serverStop() {
                axios.post('/api/server', {
                    action: 'stop'
                })
                .then(response => {
                    this.isRunning = false;
                })
                .catch(console.log);
            }
        }
    }
</script>
