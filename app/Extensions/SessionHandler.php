<?php
namespace App\Extensions;

use App\Models\UsersTime;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\ConnectionInterface;
use Illuminate\Session\DatabaseSessionHandler;
use Illuminate\Support\Facades\Route;
use SessionHandlerInterface;

class SessionHandler extends DatabaseSessionHandler
{
    /**
     * {@inheritdoc}
     */
    public function destroy($sessionId)
    {
        $this->getQuery()->where('id', $sessionId)->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function gc($lifetime)
    {
        $this->getQuery()->where('last_activity', '<=', time() - $lifetime)->delete();
    }

    public function read($sessionId)
    {
        if (Route::current()->getName() == 'set-order' || Route::current()->getName() == 'get-result-order-api') {
//        if ($_SERVER['REMOTE_ADDR'] == '145.239.121.163') {
//            return true;
        }
        $session = (object) $this->getQuery()->find($sessionId);

        if ($this->expired($session)) {
            $this->exists = true;

            return '';
        }

        if (isset($session->payload)) {
            $this->exists = true;

            return base64_decode($session->payload);
        }

        return '';
    }

    public function write($sessionId, $data)
    {
        if (Route::current()->getName() == 'set-order' || Route::current()->getName() == 'get-result-order-api') {
//        if ($_SERVER['REMOTE_ADDR'] == '145.239.121.163') {
//            return true;
        }
        $payload = $this->getDefaultPayload($data);

        if (! $this->exists) {
            $this->read($sessionId);
        }

        if ($this->exists) {
            $this->performUpdate($sessionId, $payload);
        } else {
            $this->performInsert($sessionId, $payload);
        }

        return $this->exists = true;
    }

}