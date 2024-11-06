<?php
session_start();

$link = "mysql:host=localhost;dbname=yazilimmuh";
$dbUserName = "root";
$dbPassrowd = "";

$baglanti = new PDO($link, $dbUserName, $dbPassrowd);

if ($baglanti) {    
    if (isset($_GET["logout"]) && $_GET["logout"] == 1) {
        session_destroy();
        header("Location:calisma4.php");
    }

    if (isset($_POST["kullaniciAdi"]) && isset($_POST["sifre"])) {
        $sorgu = "SELECT * FROM kullanicilar WHERE user_name = :user_name AND passw = :passw";
        $stmt = $baglanti->prepare($sorgu);
        $stmt->bindParam(":user_name", $_POST["kullaniciAdi"]);
        $stmt->bindParam(":passw", $_POST["sifre"]);
        $stmt->execute();
        $veri = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($veri) {
            $_SESSION["user_logged_in"] = true;
            if ($veri["role"] == 0) {
                echo "uygulama...<br>";
                echo "<a href = 'calisma4.php?logout=1'>Çıkış</a>";
            }
        }
        else {
            echo "Kullanıcı adı veya şifre hatalı<br>";
            echo "<a href = 'calisma4.php'>Tekrar Deneyiniz</a>";
            exit;
        }
    }

    if (isset($_SESSION["user_logged_in"])) {
        if (isset($_GET['modul']) && $_GET['modul'] == 'adminIslemleri') {
            echo "<a href = 'calisma4.php'>Ana Menü</a>&emsp;";
            echo "<a href = 'calisma4.php?modul=adminIslemleri&islem=listele'>Listele</a>&emsp;";
            echo "<a href = 'calisma4.php?modul=adminIslemleri&islem=ekle'>Ekle</a><br>";
            if (isset($_GET["islem"])) {
                $islem = $_GET["islem"];
                switch ($islem) {
                    case 'listele':
                        $sorgu = "SELECT * FROM kullanicilar";
                        $stmt = $baglanti->prepare($sorgu);
                        $stmt->execute();
                        $veri = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        echo "<table border='1'>
                                <tr>
                                    <th>ID</th>
                                    <th>Kullanıcı Adı</th>
                                    <th>Ad</th>
                                    <th>Soyad</th>
                                    <th>Rol</th>
                                    <th>İşlem</th>
                                </tr>";
                        foreach ($veri as $kullanici) {
                            $rol = ($kullanici['role'] == 1) ? 'Yönetici' : 'Kullanıcı';
                            echo "<tr>
                                    <td>{$kullanici['id']}</td>
                                    <td>{$kullanici['user_name']}</td>
                                    <td>{$kullanici['name']}</td>
                                    <td>{$kullanici['surname']}</td>
                                    <td>{$rol}</td>
                                    <td>
                                        <a href='calisma4.php?modul=adminIslemleri&islem=duzenle&id={$kullanici['id']}'>Düzenle</a> |
                                        <a href='calisma4.php?modul=adminIslemleri&islem=sil&id={$kullanici['id']}'>Sil</a>
                                    </td>
                                </tr>";
                        }
                        echo "</table>";
                        break;
                    case 'ekle':
                        if (isset($_POST["yeniKullaniciAdi"]) && isset($_POST["yeniSifre"]) && isset($_POST["yeniIsim"]) && isset($_POST["yeniSoyisim"]) && isset($_POST["yeniRol"])) {    
                            $sorgu = "INSERT INTO kullanicilar (user_name, passw, name, surname, role) VALUES (:user_name, :passw, :name, :surname, :role)";
                            $stmt = $baglanti->prepare($sorgu);
                            $stmt->bindParam(":user_name", $_POST["yeniKullaniciAdi"]);
                            $stmt->bindParam(":passw", $_POST["yeniSifre"]);
                            $stmt->bindParam(":name", $_POST["yeniIsim"]);
                            $stmt->bindParam(":surname", $_POST["yeniSoyisim"]);
                            $stmt->bindParam(":role", $_POST["yeniRol"]);
                            $stmt->execute();
                            echo "Kullanıcı Eklendi<br>";
                        }
                        else {
                            ?>
                            <form method="POST" action="calisma4.php?modul=adminIslemleri&islem=ekle">
                                <table>
                                    <tr>
                                        <td>Kullanıcı Adı:</td>
                                        <td><input type="text" name="yeniKullaniciAdi"></td>
                                    </tr>
                                    <tr>
                                        <td>Şifre:</td>
                                        <td><input type="text" name="yeniSifre"></td>
                                    </tr>
                                    <tr>
                                        <td>Ad:</td>
                                        <td><input type="text" name="yeniIsim"></td>
                                    </tr>
                                    <tr>
                                        <td>Soyad:</td>
                                        <td><input type="text" name="yeniSoyisim"></td>
                                    </tr>
                                    <tr>
                                        <td>Rol:</td>
                                        <td>
                                            <select name="yeniRol">
                                                <option value="0">Kullanıcı</option>
                                                <option value="1">Yönetici</option>
                                            </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2"><input type="submit" value="Ekle"></td>
                                    </tr>
                                </table>
                            </form>
                            <?php
                        }
                        break;
                    case 'duzenle':
                        if (isset($_GET["id"])) {
                            if (isset($_POST["yeniKullaniciAdi"]) && isset($_POST["yeniSifre"]) && isset($_POST["yeniIsim"]) && isset($_POST["yeniSoyisim"]) && isset($_POST["yeniRol"])) {    
                                $sorgu = "UPDATE kullanicilar SET user_name = :user_name, passw = :passw, name = :name, surname = :surname, role = :role WHERE id = :id";
                                $stmt = $baglanti->prepare($sorgu);
                                $stmt->bindParam(":user_name", $_POST["yeniKullaniciAdi"]);
                                $stmt->bindParam(":passw", $_POST["yeniSifre"]);
                                $stmt->bindParam(":name", $_POST["yeniIsim"]);
                                $stmt->bindParam(":surname", $_POST["yeniSoyisim"]);
                                $stmt->bindParam(":role", $_POST["yeniRol"]);
                                $stmt->bindParam(":id", $_GET["id"]);
                                $stmt->execute();
                                echo "Kullanıcı Düzenlendi<br>";
                            }
                            else {
                                $sorgu = "SELECT * FROM kullanicilar WHERE id = :id";
                                $stmt = $baglanti->prepare($sorgu);
                                $stmt->bindParam(":id", $_GET["id"]);
                                $stmt->execute();
                                $kullanici = $stmt->fetch(PDO::FETCH_ASSOC);
                                ?>
                                    <form method="POST" action="calisma4.php?modul=adminIslemleri&islem=duzenle&id=<?php echo $_GET["id"]; ?>">
                                        <table>
                                            <tr>
                                                <td>Kullanıcı Adı:</td>
                                                <td><input type="text" name="yeniKullaniciAdi" value="<?php echo $kullanici['user_name']; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td>Şifre:</td>
                                                <td><input type="text" name="yeniSifre" value="<?php echo $kullanici['passw']; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td>Ad:</td>
                                                <td><input type="text" name="yeniIsim" value="<?php echo $kullanici['name']; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td>Soyad:</td>
                                                <td><input type="text" name="yeniSoyisim" value="<?php echo $kullanici['surname']; ?>"></td>
                                            </tr>
                                            <tr>
                                                <td>Rol:</td>
                                                <td>
                                                    <select name="yeniRol">
                                                        <option value="0" <?php if($kullanici['role'] == 0) {echo 'selected';} ?>>Kullanıcı</option>
                                                        <option value="1" <?php if($kullanici['role'] == 1) {echo 'selected';} ?>>Yönetici</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2"><input type="submit" value="Kaydet"></td>
                                            </tr>
                                        </table>
                                    </form>
                                    <?php
                            }
                        }
                        break;
                    case 'sil':
                        if (isset($_GET["id"])) {
                            $sorgu = "DELETE FROM kullanicilar WHERE id = :id";
                            $stmt = $baglanti->prepare($sorgu);
                            $stmt->bindParam(":id", $_GET["id"]);
                            $stmt->execute(); 
                            echo "Kullanıcı Silindi.<br>";
                        }
                        break;
                }
            }
            exit;
        }
        else {
            echo "<a href = 'calisma4.php?modul=adminIslemleri'>Admin İşlemleri</a><br>";
            echo "uygulama...<br>";
            echo "<a href = 'calisma4.php?logout=1'>Çıkış</a>";
            exit;
        }
    }
}
else {
    echo "Sunucuya bağlanılamadı. Daha sonra tekrar deneyiniz.";
    exit;
}
?>

<form method="POST" name="login" action="calisma4.php">
    Kullanıcı Adı: <input type="text" name="kullaniciAdi" id="kullaniciAdi">
    <br>
    Şifre: <input type="password" name="sifre" id="sifre">
    <br>
    <input type="submit" name="Gönder" value="Giriş Yap">
</form>
