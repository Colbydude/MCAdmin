<template>
    <div class="card">
        <div class="card-header">Console</div>
        <div class="card-body">
            <div class="console" @scroll="onScroll" v-html="lines"></div>
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
                atBottom: true,     // Whether or not we're scrolled to the bottom of the console.
                input: '',          // Console command input.
                lines: ''           // Console output.
            };
        },

        mounted() {
            this.getServerLog();
        },

        methods: {
            /**
             * Gets the most recent server output.
             *
             * @return {Void}
             */
            getServerLog() {
                axios.get('/api/server/log')
                .then(response => {
                    this.lines = response.data;

                    // Scroll automatically if we're at the bottom of the console.
                    if (this.atBottom && this.isRunning) {
                        let consoleDiv = document.querySelector('.console');
                        consoleDiv.scrollTop = consoleDiv.scrollHeight;
                    }

                    // Fetch data in another second.
                    window.setTimeout(this.getServerLog, 1000);
                })
                .catch(console.log);
            },

            /**
             * Debounce console scroll.
             *
             * @return {Void}
             */
            onScroll: _.debounce(function (e) {
                this.scrolled();
            }, 100, { 'maxWait': 1000 }),

            /**
             * Determine if we've scrolled to the bottom of the element.
             *
             * @return {Void}
             */
            scrolled() {
                let consoleDiv = document.querySelector('.console');

                if (consoleDiv.scrollTop === (consoleDiv.scrollHeight - consoleDiv.offsetHeight)) {
                    this.atBottom = true;
                } else {
                    this.atBottom = false;
                }
            },

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
        }
    };
</script>
