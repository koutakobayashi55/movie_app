var server = require("ws").Server;
var s = new server({port:80});
s.on("connection", (ws) => {
    ws.on("comment", (input_comment) => {
        console.log(input_comment);
    }),
    ws.on("message", (dataToSend) => {
        const data = JSON.parse(dataToSend);
        const user = data.user;
        const flag = data.status;
        const comment = data.comment;

        if (user && flag && comment) {
            console.log("Recv:「" + comment + "」 とコメントが入力されました");
            console.log("Send:「" + comment + "」 とコメントが入力されました");
        } else if (user && flag) {
            console.log("Recv:" + user + "が参加しました");
            console.log("Send:" + user + "が参加しました");
        } else {
            console.log("Recv:" + user + "が退出しました");
            console.log("Send:" + user + "が退出しました");
        }
        s.clients.forEach((client) => {
            client.send(dataToSend.toString());
        });
    });
    ws.on("close", () => {
        console.log("Closed.");
    });
});
