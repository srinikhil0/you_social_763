<?php
class Message {
    private $user_obj;
    private $con;

    public function __construct($con, $user){
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function startNewSession($user1, $user2) {
        $key = $this->generateUniqueKey();
        $this->storeKey($key, $user1, $user2);
        return $key;
    }

    private function generateUniqueKey() {
        return bin2hex(random_bytes(32)); // Generates a 256-bit key
    }

    private function encryptMessage($message, $key, $iv) {
        return openssl_encrypt($message, 'aes-256-cbc', hex2bin($key), 0, $iv);
    }

    private function decryptMessage($encryptedMessage, $iv, $encryptionKey) {
        return openssl_decrypt($encryptedMessage, 'aes-256-cbc', hex2bin($encryptionKey), 0, hex2bin($iv));
    }

    public function getMostRecentUser() {
        $userLoggedIn = $this->user_obj->getUsername();
        $stmt = $this->con->prepare("SELECT user_to, user_from FROM messages WHERE user_to=? OR user_from=? ORDER BY id DESC LIMIT 1");
        $stmt->bind_param("ss", $userLoggedIn, $userLoggedIn);
        $stmt->execute();
        $result = $stmt->get_result();
        if($result->num_rows == 0)
            return false;
        $row = $result->fetch_assoc();
        $stmt->close();
        if($row['user_to'] != $userLoggedIn)
            return $row['user_to'];
        else 
            return $row['user_from'];
    }

    public function sendMessage($user_to, $body, $date) {
        if($body != "") {
            $userLoggedIn = $this->user_obj->getUsername();
            $sessionKey = $this->generateUniqueKey();
            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
            $encryptedBody = $this->encryptMessage($body, $sessionKey, $iv);
            $this->storeKey($sessionKey, $iv, $userLoggedIn, $user_to, $encryptedBody, $date);
        }
    }

    private function storeKey($key, $iv, $user_from, $user_to, $encryptedBody, $date) {
        $stmt = $this->con->prepare("INSERT INTO messages (user_to, user_from, body, iv, date, opened, viewed, deleted, encryption_key) VALUES (?, ?, ?, ?, ?, 'no', 'no', 'no', ?)");
        $stmt->bind_param("ssssss", $user_to, $user_from, $encryptedBody, $iv, $date, $key);
        $stmt->execute();
        $stmt->close();
    }

    public function getMessages($otherUser) {
        $userLoggedIn = $this->user_obj->getUsername();
        $stmt = $this->con->prepare("UPDATE messages SET opened='yes' WHERE user_to=? AND user_from=?");
        $stmt->bind_param("ss", $userLoggedIn, $otherUser);
        $stmt->execute();
        $stmt->close();

        $stmt = $this->con->prepare("SELECT * FROM messages WHERE (user_to=? AND user_from=?) OR (user_from=? AND user_to=?)");
        $stmt->bind_param("ssss", $userLoggedIn, $otherUser, $userLoggedIn, $otherUser);
        $stmt->execute();
        $result = $stmt->get_result();
        $data = "";
        while($row = $result->fetch_assoc()) {
            $body = $this->decryptMessage($row['body'], $row['iv'], $row['encryption_key']);
            $div_top = ($row['user_to'] == $userLoggedIn) ? "<div class='message' id='green'>" : "<div class='message' id='blue'>";
            $data .= $div_top . $body . "</div><br><br>";
        }
        $stmt->close();
        return $data;
    }
}
?>
