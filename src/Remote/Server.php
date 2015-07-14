<?php

namespace Kohkimakimoto\Luster\Remote;

class Server
{
    public $host;

    public $port;

    public $user;

    public $key;

    public $keyPassphrase;

    public $useAgent;

    public $pty;

    public function __construct(array $params = [])
    {
        if (isset($params['host'])) {
            $this->host = $params['host'];
        }

        if (isset($params['port'])) {
            $this->port = $params['port'];
        } else {
            $this->port = '22';
        }

        if (isset($params['user'])) {
            $this->user = $params['user'];
        } else {
            $this->user = get_current_user();
        }

        if (isset($params['key'])) {
            $this->key = $params['key'];
        } else {
            $this->key = getenv('HOME').'/.ssh/id_rsa';
        }

        if (isset($params['key_passphrage'])) {
            $this->keyPassphrage = $params['key_passphrage'];
        }

        if (isset($params['use_agent'])) {
            $this->useAgent = $params['use_agent'];
        } else {
            $this->useAgent = false;
        }

        if (isset($params['pty'])) {
            $this->pty = $params['pty'];
        } else {
            $this->pty = false;
        }
    }

    public function getSSHConnection()
    {
        $ssh = new \Net_SSH2(
            $this->host,
            $this->port
        );

        $key = new \Crypt_RSA();

        if ($this->useAgent) {
            // use ssh-agent
            if (class_exists('System_SSH_Agent', true) == false) {
                require_once 'System/SSH_Agent.php';
            }
            $key = new \System_SSH_Agent();
        } else {
            // use ssh key file
            if ($this->isUsedWithPassphrase()) {
                // use passphrase
                $key->setPassword($this->keyPassphrase);
            }

            if (!$key->loadKey($this->getKeyContents())) {
                throw new \RuntimeException('Unable to load SSH key file: '.$this->key);
            }
        }

        if ($this->pty) {
            $ssh->enablePTY();
        }
        
        // login
        if (!$ssh->login($this->user, $key)) {
            $err = error_get_last();
            $emessage = isset($err['message']) ? $err['message'] : '';
            throw new \RuntimeException('Unable to login '.$this->user.'. '.$emessage);
        }

        return $ssh;
    }

    protected function isUsedWithPassphrase()
    {
        return SSHKey::hasPassphrase($this->getKeyContents());
    }

    protected function getKeyContents()
    {
        return file_get_contents($this->key);
    }
}
