<?php
require_once('type.php');
/**
 * Handles all form text input.
 * Works together with type.php to validate input.
 * 
 * Instructions:
 * - include the file
 * - call init()
 * - use input() to take textual input
 * - use updateSession() BEFORE the form code
 * SESSION format:
 * $_SESSION[$SESSION_NAME] = [
 *  [id_1] => [content => 'content', error => 'error', type => 'type', required => 'required'],
 *  [id_2] => [content => 'content', error => 'error', type => 'type', required => 'required'],
 *  etc...
 * ]
 */
class Form
{
    public static $SESSION_NAME;
    public static function init()
    {
        if (!isset($_SESSION[self::$SESSION_NAME])) {
            $_SESSION[self::$SESSION_NAME] = [];
        }
    }
    public static function inputText(string $id, string $type, string $placeholder = '', string $icon = '', bool $required = false, bool $unique = false, bool $caseSensitive = true)
    {
        self::addSession($id, $type, $required, $unique, $caseSensitive);

        echo "  <div class='input-box'>
                    <input type='text' name='$id' placeholder='$placeholder' value='{$_SESSION[self::$SESSION_NAME][$id]['content']}'>
                    $icon
                    <div class='err-msg'> <p>{$_SESSION[self::$SESSION_NAME][$id]['error']}</p> </div>
                </div>";
    }
    public static function inputPassword(string $id, string $placeholder = '', string $icon = '', bool $required = false)
    {
        self::addSession($id, Type::$Password, $required, false, true);

        echo "  <div class='input-box'>
                    <input type='password' id='$id' name='$id' placeholder='$placeholder' value='{$_SESSION[self::$SESSION_NAME][$id]['content']}'>
                    $icon
                    <div class='err-msg'> <p>{$_SESSION[self::$SESSION_NAME][$id]['error']}</p> </div>
                    <input type='checkbox' id='checkbox_$id' name='checkbox_$id' onclick='myFunction(\"$id\")'>
                    <label for='checkbox_$id' class='custom-checkbox'></label>
                </div>";
    }

    private static function addSession(string $id, string $type, bool $required = false, bool $unique, bool $caseSensitive)
    {
        if (isset($_SESSION[self::$SESSION_NAME][$id])) {
            return;
        }

        $_SESSION[self::$SESSION_NAME][$id] = ['content' => '', 'error' => '', 'type' => $type, 'required' => $required, 'unique' => $unique, 'caseSensitive' => $caseSensitive];
    }
    public static function updateContents()
    {
        foreach ($_POST as $id => $content) {
            if ($id === 'submit' || $id == 'refresh') {
                continue;
            }

            if (isset($_SESSION[self::$SESSION_NAME][$id])) {
                $_SESSION[self::$SESSION_NAME][$id]['content'] = $content;
            }
        }
    }
    public static function updateErrors()
    {
        foreach ($_SESSION[self::$SESSION_NAME] as $id => $key) {
            if ($id == 'submit' || $id == 'refresh') {
                continue;
            }

            $errMsg = '';
            //check for blank input
            if ($key['required'] && $key['content'] === '') {
                $errMsg = 'Required <br>';
            } elseif (!Type::checkValid($key['content'], $key['type'])) {
                $errMsg = Type::errMsg($key['type']) . '<br>';
            }

            //update session error
            $_SESSION[self::$SESSION_NAME][$id]['error'] = $errMsg;
        }
    }
    public static function hasErrors()
    {
        foreach ($_SESSION[self::$SESSION_NAME] as $id => $key) {
            if ($id == 'submit' || $id == 'refresh') {
                continue;
            }

            if ($key['error'] !== '') {
                return true;
            }
        }

        return false;
    }
}
