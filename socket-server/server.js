const http = require('http');
const socketIo = require('socket.io');

// const server = http.createServer((req, res) => {
//     if (req.method === 'OPTIONS') {
//         res.setHeader('Access-Control-Allow-Origin', req.headers.origin || '*');
//         res.setHeader('Access-Control-Allow-Methods', 'GET, POST');
//         res.setHeader('Access-Control-Allow-Headers', 'Content-Type');
//         return res.end();
//     }
//     res.writeHead(200, { 'Content-Type': 'text/plain' });
//     res.end('Socket.IO server is running');
// });

const server = http.createServer();

const io = socketIo(server, {
    cors: {
        // origin: (origin, callback) => {
        //     const allowedOrigins = [
        //         'http://forex.test',
        //         'http://forex.dev.sinag',
        //         'http://forex.stg.sinag',
        //         'http://192.168.88.25:3434',
        //     ];

        //     if (!origin || allowedOrigins.includes(origin)) {
        //         callback(null, true);
        //     } else {
        //         callback(new Error('Not allowed by CORS'));
        //     }
        // },
        origin: "*",
        methods: ["GET", "POST"],
        credentials: false,
    }
});

io.on('connection', (socket) => {
    let connectedUsers = {};

    // socket.on('notification', (msg) => {
    //     console.log('Message received:', msg);
    //     io.emit('notification', msg);
    // });

    socket.on('message', (msg) => {
        console.log('Message received:', msg);
        io.emit('message', msg);
    });

    socket.on('branchPrompt', (msg) => {
        console.log('Message received:', msg);
        io.emit('branchPrompt', msg);
    });

    socket.on('revertBuffer', (msg) => {
        console.log('Message received:', msg);
        io.emit('revertBuffer', msg);
    });

    socket.on('transferCapital', (msg) => {
        console.log('Message received:', msg);
        io.emit('transferCapital', msg);
    });

    socket.on('ATDEPS', (msg) => {
        console.log('Message received:', msg);
        io.emit('ATDEPS', msg);
    });

    socket.on('stopBuyingChanges', (msg) => {
        console.log('Message received:', msg);
        io.emit('stopBuyingChanges', msg);
    });

    socket.on('updateRate', (msg) => {
        console.log('Message received:', msg);
        io.emit('updateRate', msg);
    });

    socket.on('userConnected', (data) => {
        const userId = data.userId;
        connectedUsers[userId] = socket.id;
        console.log(`User connected: ${userId} (Socket ID: ${socket.id})`);
    });

    socket.on('disconnect', () => {
        console.log(`User with socket ID: ${socket.id} disconnected`);

        for (const userId in connectedUsers) {
            if (connectedUsers[userId] === socket.id) {
                console.log(`User disconnected: ${userId}`);
                delete connectedUsers[userId];
                break;
            }
        }
    });
});

server.listen(9090, () => {
    console.log('Socket.IO server running on port 9090');
});
