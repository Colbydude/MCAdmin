import { Terminal } from 'xterm';
import { Sentry } from 'zmodem.js'
import 'xterm/dist/xterm.css';

Terminal.applyAddon(require('xterm/lib/addons/fit/fit'));

/**
 * Wrapper around WebSocket and Terminal for communicating with ttyd.
 *
 * @class Ttyd
 */
export default class Ttyd {
    /**
     * Construct a new instace of the socket.
     *
     * @param  {String}  url
     * @param  {String}  authToken
     * @constructor
     */
    constructor(url, authToken = null) {
        this.authToken = authToken;
        this.decoder = new TextDecoder();
        this.encoder = new TextEncoder();
        this.url = url;

        this.openWebSocket();
    }

    /**
     * Create a new WebSocket connection to ttyd.
     *
     * @return {Void}
     */
    openWebSocket() {
        this.ws = new WebSocket(this.url, ['tty']);

        let container = this;
        this.zsentry = new Sentry({
            to_terminal: function _to_terminal(octets) {
                let buffer = new Uint8Array(octets).buffer;
                container.term.write(container.decoder.decode(buffer));
            },

            sender: function _ws_sender_func(octets) {
                // Limit max packet size to 4096.
                while (octets.length) {
                    let chunk = octets.splice(0, 4095);
                    let buffer = new Uint8Array(chunk.length + 1);

                    buffer[0] = '0'.charCodeAt(0);
                    buffer.set(chunk, 1);

                    container.ws.send(buffer);
                }
            },

            on_retract: function _on_retract() {
                // console.log('on_retract');
            },

            on_detect: function _on_detect(detection) {
                // console.log('on_detect');
            }
        });

        this.ws.binaryType = 'arraybuffer';

        this.ws.onopen = this.onOpen.bind(this);
        this.ws.onmessage = this.onMessage.bind(this);
        this.ws.onclose = this.onClose.bind(this);
    }

    /**
     *
     *
     * @param  {String}  message
     * @return {Void}
     */
    sendMessage(message) {
        if (this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(this.encoder.encode(message));
        }
    }

    /**
     *
     *
     * @param  {*}  data
     * @return {Void}
     */
    sendData(data) {
        this.sendMessage('0' + data);
    };

    /**
     *
     *
     * @param  {*}  event
     * @return {String}
     */
    unloadCallback(event) {
        let message = 'Close terminal? This will also terminate the command.';
        (event || window.event).returnValue = message;

        return message;
    }

    /**
     *
     *
     * @return {Void}
     */
    resetTerm() {
        clearTimeout(this.reconnectTimer);

        if (this.ws.readyState !== WebSocket.CLOSED) {
            this.ws.close();
        }

        this.openWebSocket();
    }

    /**
     * On WebSocket connection callback.
     *
     * @param  {}  event
     * @return {Void}
     */
    onOpen(event) {
        console.log('Websocket connection opened.');

        this.sendMessage(JSON.stringify({AuthToken: this.authToken}));

        if (typeof this.term !== 'undefined') {
            this.term.dispose();
        }

        let terminalContainer = document.getElementById('terminal');

        this.term = window.term = new Terminal({
            fontSize: 13,
            fontFamily: '"Source Code Pro", "Menlo for Powerline", Menlo, Consolas, "Liberation Mono", Courier, monospace',
            theme: {
                foreground: '#d2d2d2',
                background: '#2b2b2b',
                cursor: '#adadad',
                black: '#000000',
                red: '#d81e00',
                green: '#5ea702',
                yellow: '#cfae00',
                blue: '#427ab3',
                magenta: '#89658e',
                cyan: '#00a7aa',
                white: '#dbded8',
                brightBlack: '#686a66',
                brightRed: '#f54235',
                brightGreen: '#99e343',
                brightYellow: '#fdeb61',
                brightBlue: '#84b0d8',
                brightMagenta: '#bc94b7',
                brightCyan: '#37e6e8',
                brightWhite: '#f1f1f0'
            }
        });

        this.term.on('resize', (size) => {
            if (this.ws.readyState === WebSocket.OPEN) {
                this.sendMessage('1' + JSON.stringify({columns: size.cols, rows: size.rows}));
            }
        });

        this.term.on('data', this.sendData.bind(this));

        while (terminalContainer.firstChild) {
            terminalContainer.removeChild(terminalContainer.firstChild);
        }

        window.addEventListener('resize', () => {
            clearTimeout(window.resizeFinished);
            window.resizeFinished = setTimeout(() => {
                this.term.fit();
            }, 250);
        });
        window.addEventListener('beforeunload', this.unloadCallback);

        this.term.open(terminalContainer, true);
        this.term.fit();
        this.term.focus();
    }

    /**
     * On WebSocket message callback.
     *
     * @param  {}  event
     * @return {Void}
     */
    onMessage(event) {
        let rawData = new Uint8Array(event.data),
            cmd = String.fromCharCode(rawData[0]),
            data = rawData.slice(1).buffer;

        switch (cmd) {
            case '0':
                try {
                    this.zsentry.consume(data);
                } catch (e) {
                    console.error(e);
                    this.resetTerm();
                }
            break;

            case '1':
                // @TODO
            break;

            case '2':
                let preferences = JSON.parse(this.decoder.decode(data));

                Object.keys(preferences).forEach(key => {
                    console.log('Setting ' + key + ': ' + preferences[key]);
                    this.term.setOption(key, preferences[key]);
                });
            break;

            case '3':
                this.autoReconnect = JSON.parse(this.decoder.decode(data));
                console.log('Enabling reconnect: ' + this.autoReconnect + ' seconds');
            break;

            default:
                console.log('Unknown command: ' + cmd);
            break;
        }
    }

    /**
     * On WebSocket close callback.
     *
     * @param  {}  event
     * @return {Void}
     */
    onClose(event) {
        console.log('Websocket connection closed with code: ' + event.code);
        if (this.term) {
            this.term.off('data');
            this.term.off('resize');
        }

        window.removeEventListener('beforeunload', this.unloadCallback);

        if (event.code !== 1000 && this.autoReconnect > 0) {
            this.reconnectTimer = setTimeout(this.openWebSocket, this.autoReconnect * 1000);
        }
    }
}
