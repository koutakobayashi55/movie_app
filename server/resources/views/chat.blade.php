{{-- ユーザー名入力欄 --}}
<div class="handle-div">
    <input class="handle" type="text" id="msg" style="blue" name="handle_name" value="" placeholder="ユーザー名">
    <div class="connect">
        <button id="connect_button" style="cursor: pointer;">参加する</button>
    </div>
</div>

{{-- 参加人数表示欄 --}}
<div class="count-div">
    <span>参加人数：</span><span id="user-count" class="user-count">--</span><span class="count-unit">人</span>
</div>

{{-- チャット履歴欄 --}}
<div class="table_box">
    <div class="message-area" style="height: 80vh; background: #8DB4E5; overflow: scroll;">
        <pre id="out"></pre>
    </div>
    <div class="comment_box">
        {{-- コメント入力欄 --}}
        <div class="comment-div" style="display: block;">
            <input class="comment" type="text" id="comment" name="comment" value="" placeholder="コメント">
        </div>
        <div class="send">
            <button class="send_button" id="send_button" style="cursor: pointer;">送信</button>
        </div>
    </div>
</div>

{{-- 非表示エリア --}}
<table style="display: none;">
    <tbody id="template">
        <tr><td class="partition" colspan="2"></td></tr>
        <tr>
            <td class="datetime"></td>
            <td class="handle"></td>
        </tr>
        <tr>
            <td class="comment" colspan="2"></td>
        </tr>
    </tbody>
</table>

<style>
    .comment_box {
        height: 5vh;
        display: flex;
    }

    .comment-div {
        height: 5vh;
        display: flex;
        width: 80%;
    }

    .comment-div input {
        width: 100%;
        height: 100%;
        resize: none;
        border: 1px solid #ddd;
        box-sizing: border-box;
    }

    .send_button {
        width: 20%;
    }

    .send button {
        width: 100%;
        height: 100%;
        font-size: 1.0rem;
        font-weight: bold;
        border: 0;
    }

    .handle-div {
        height: 5vh;
        display: flex;
        width: 30%;
    }

    .handle-div input {
        width: 100%;
        height: 100%;
        resize: none;
        border: 1px solid #ddd;
        box-sizing: border-box;
    }

    .connect_button {
        width: 40%;
    }

    .connect button {
        width: 130%;
        height: 100%;
        font-size: 0.7rem;
        font-weight: bold;
        border: 0;
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    var out = document.getElementById("out");
    var user_count_elm = document.getElementById("user-count");
    var msg = document.getElementById("msg");
    var comment = document.getElementById("comment");
    var btn = document.getElementById("btn");
    var connect_button = document.getElementById("connect_button");
    var send_button = document.getElementById("send_button");
    var sock = new WebSocket("ws://127.0.0.1:80/");
    var flg_closing;
    var input_user = document.getElementById("msg").value;
    var dataToSend;
    var count;
    var user_count = 0;
    var date;
    var date_time;
    var uniq_flg = false;
    var user_id;
    var messageArea = document.querySelector('.message-area');

    /**
     * ユニークIDを生成
     * @param {number} [digits=1000] 末尾に付与する乱数の桁
     * @return {string} 生成したユニークIDを返す
    */
    function uniqueId(digits) {
        var strong = typeof digits !== 'undefined' ? digits : 1000;
        return Date.now().toString(16) + Math.floor(strong * Math.random()).toString(16);
    };

    // メッセージ受信時の処理
    sock.addEventListener("message", (e) => {
        // ユーザー名取得
        const data = JSON.parse(e.data);

        // 日付の取得
        date = new Date();
        date_time = date.getFullYear() + '/' + ('0' + (date.getMonth() + 1)).slice(-2) + '/' +('0' + date.getDate()).slice(-2) + ' ' +  ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2) + ':' + ('0' + date.getSeconds()).slice(-2);

        if (data.user_id === data.user_id) {
            console.log("ユニークID", data.user_id)
        }
        console.log("User.", data.user, "flag", data.status, "comment", data.comment, "uniqueId", data.user_id);

        // コメント欄に参加したら、日時とユーザー名と参加コメントを表示
        if (data.status && data.user.trim() !== "" && data.comment === undefined) {
            out.innerHTML += `<span>${date_time} <strong>${data.user}</strong> ID:${data.user_id}</span><br>`;
            out.innerHTML += `<span style="color: blue; font-weight: bold;">参加しました</span><br>`;

            // 参加人数をプラス 1 、カウント
            user_count += 1;

        // コメントが入力されたら、ユーザー名と一緒に表示
        } else if (data.status && data.user.trim() !== "" && data.comment) {
            out.innerHTML += `<span>${date_time} <strong>${data.user}</strong> ID:${data.user_id}</span><br>`;
            out.innerHTML += `<span style="color: blue; font-weight: bold;">${data.comment}</span><br>`;

            // スクロールを一番下に移動
            messageArea.scrollTop = messageArea.scrollHeight;

        // コメント欄から退室したら、日時とユーザー名と退室コメントを表示
        } else if (!data.status && data.user.trim() !== "" && data.comment === undefined) {
            out.innerHTML += `<span>${date_time} <strong>${data.user}</strong> ID:${data.user_id}</span><br>`;
            out.innerHTML += `<span style="color: blue; font-weight: bold;">退室しました</span><br>`;

            // 参加人数のカウントをマイナス 1
            user_count -= 1;
        }
        // 参加人数を表示
        user_count_elm.innerText = user_count;
    });

    // ボタンクリック時の処理
    connect_button.addEventListener("click", (e) => {
        // ユーザー名を取得
        input_user = document.getElementById("msg").value;

        // [参加する]ボタン押下で、 [退室する]ボタンに
        if (connect_button.innerText === '参加する' && input_user !== "") {
            // ユニークIDが生成されていなければ作成
            if (!uniq_flg) {
                uniq_flg = true;
                user_id = uniqueId();
            }

            // ボタン名変更
            $('#connect_button').text('退室する');

            // ハンドルネーム入力を禁止
            $('input[name="handle_name"]').prop('disabled', true);
            // コメント入力を許可
            $('input[name="comment"]').prop('disabled', false);

            // 入退室判断のフラグをたてる
            flg_closing = true

            dataToSend = {
                user: input_user,
                status: flg_closing,
                user_id: user_id
            }

            // データを JSON 形式に変換して入室メッセージを送信
            sock.send(JSON.stringify(dataToSend));

        // [退室する]ボタン押下で、 [参加する]ボタンに
        } else if (connect_button.innerText === '退室する' && input_user !== "") {
            // ボタン名変更
            $('#connect_button').text('参加する');

            // ハンドルネーム入力を許可
            $('input[name="handle_name"]').prop('disabled', false);
            // コメント入力を禁止
            $('input[name="comment"]').prop('disabled', true);

            // 入退室判断のフラグをたてる
            flg_closing = false

            // ユニークIDのフラグ
            uniq_flg = false

            dataToSend = {
                user: input_user,
                status: flg_closing,
                user_id: user_id,
                uniq_flg: uniq_flg
            };

            // データを JSON 形式に変換して退出メッセージを送信
            sock.send(JSON.stringify(dataToSend));
        } else {
            alert("ユーザー名を入力してください。");
        }
    });

    // ユーザー名が入力されていない初期状態はコメント入力不可
    $('input[name="comment"]').prop('disabled', true);

    // コメント送信ボタンが押下された時の処理
    send_button.addEventListener("click", (e) => {
        // ユーザー名を入力してチャットに参加を必須条件
        if (flg_closing) {
            // 日付の取得
            date = new Date();
            date_time = date.getFullYear() + '/' + ('0' + (date.getMonth() + 1)).slice(-2) + '/' +('0' + date.getDate()).slice(-2) + ' ' +  ('0' + date.getHours()).slice(-2) + ':' + ('0' + date.getMinutes()).slice(-2) + ':' + ('0' + date.getSeconds()).slice(-2);

            // ユーザー名とコメントを取得
            input_user = document.getElementById("msg").value;
            input_comment = document.getElementById("comment").value;

            // コメントは入力必須
            if (input_comment == "") {
                alert("コメントを入力してください。")
            } else {

                // コメントが入力・送信されたら入力コメントをクリア
                document.getElementById("comment").value = "";
                dataToSend = {
                    user: input_user,
                    status: flg_closing,
                    comment: input_comment,
                    user_id: user_id
                }

                // データを JSON 形式に変換して入室メッセージを送信
                sock.send(JSON.stringify(dataToSend));
            }
        } else {
            alert("チャットに参加してから、コメントしてください。");
        }
    });

    sock.addEventListener("close", (e) => {
        console.log("Closed.");
    });

    sock.addEventListener("open", (e) => {
        console.log("Connected.");
    });

    sock.addEventListener("error", (e) => {
        console.log("Error.");
    });
</script>
