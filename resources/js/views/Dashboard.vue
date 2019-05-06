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

                <div class="card mb-4">
                    <div class="card-header">Server Config</div>
                    <div class="card-body">
                        <table class="table table-striped table-sm table-borderless">
                            <tbody>
                                <tr>
                                    <td><strong>Server Directory:</strong></td>
                                    <td><code>{{ config.directory }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Server JAR:</strong></td>
                                    <td><code>{{ config.jar }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Startup RAM:</strong></td>
                                    <td><code>{{ config.startupRam }}</code></td>
                                </tr>
                                <tr>
                                    <td><strong>Max RAM:</strong></td>
                                    <td><code>{{ config.maxRam }}</code></td>
                                </tr>
                            </tbody>
                        </table>
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
                config: window.config.serverConfig,
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
