<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $name
 * @property string $surname
 * @property string $password
 * @property string $salt
 * @property string $access_token
 * @property string $create_date
 */
class User extends ActiveRecord implements IdentityInterface
{
    const MAX_LOGIN = 128;
    const MIN_PASSWORD = 6;
    const MAX_NAME_ANDSURNAME = 45;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'password'], 'required'],
            [['username'], 'string', 'max' => self::MAX_LOGIN],
            [['password'],'string','min' => self::MIN_PASSWORD],
            [['name', 'surname'], 'string', 'max' => self::MAX_NAME_ANDSURNAME],
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'username' => Yii::t('app', 'Login'),
            'name' => Yii::t('app', 'Имя'),
            'surname' => Yii::t('app', 'Фамилия'),
            'password' => Yii::t('app', 'Пароль')
            
            
        ];
    }

    /**
     * @inheritdoc
     * @return \app\models\query\UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\query\UserQuery(get_called_class());
    }
    /*Реализация IdentytiInterface*/
    public static function findIdentity($id) {
        return static::findOne(['id'=>$id]);
    }
    
    public static function findIdentityByAccessToken($token, $type = null) {
        return static::findOne(["access_token"=>$token]);
    }
    
    public function getId() {
        return $this->id;
    }
    
    public function getAuthKey() {
        return $this->access_token;
    }
    
    public function validateAuthKey($authKey) {
        return $this->getAuthKey()==$authKey;
    }
    /*IdentityInterface реализован*/
    // Создает последовательность символов добавляемую  к паролю
    public function saltGenerator(){
        return hash("sha512", uniqid('salt_', true));
    }
    //Добавляет последовательность символов созданную saltGenerator к паролю
    public function passwordWithSalt($password, $salt){
        return hash("sha512", $password.$salt);
    }
    //генерируем access_token
    public function generateAccessToken(){
        $this->access_token=Yii::$app->security->generateRandomString();
    }
    //Пароль с шифрованием
    public function setPassword($password){
        $this->password = $this->passwordWithSalt($password, $this->saltGenerator());
    }
     //Проверка пароля
    public function validatePassword($password){
       return $this->password===$this->passwordWithSalt($password, $this->salt);
    }
    
    public static function findByUsername ($username)
    {
        return static::findOne(['username' => $username]);
    }
    //Перед добавлением записи в БД Кодируем пароль
    
    public function beforeSave($insert) {
        if(parent::beforeSave($insert))
        {
            if ($this->getIsNewRecord() && !empty($this->password)){
                $this->salt = $this->saltGenerator(); 
            }
            if (!empty($this->password)){
                $this->password=  $this->passwordWithSalt($this->password, $this->salt);
            }
            else{
                unset($this->password);
            }
            return true;
        }
        else
        {
            return false;
        }
    }
}
