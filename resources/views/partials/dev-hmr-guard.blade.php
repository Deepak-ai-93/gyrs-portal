@if (app()->environment('local'))
    <script>
        (function () {
            var NativeWebSocket = window.WebSocket;

            if (!NativeWebSocket) {
                return;
            }

            var sockets = [];

            function GuardedWebSocket(url, protocols) {
                var socket = protocols === undefined
                    ? new NativeWebSocket(url)
                    : new NativeWebSocket(url, protocols);

                sockets.push(socket);

                return socket;
            }

            GuardedWebSocket.prototype = NativeWebSocket.prototype;
            ['CONNECTING', 'OPEN', 'CLOSING', 'CLOSED'].forEach(function (state) {
                GuardedWebSocket[state] = NativeWebSocket[state];
            });

            window.WebSocket = GuardedWebSocket;

            /*
             * The browser severs open WebSockets when a page enters the
             * back-forward cache, and Vite's HMR client reports that as a
             * console error. Closing the socket first lets the client
             * reconnect cleanly when the page is restored.
             */
            window.addEventListener('pagehide', function () {
                sockets.forEach(function (socket) {
                    try {
                        if (socket.readyState === NativeWebSocket.OPEN) {
                            socket.close(1000, 'pagehide');
                        }
                    } catch (error) {
                        // The socket may already be closing.
                    }
                });
            });
        })();
    </script>
@endif
