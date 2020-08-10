<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Server;
use phpseclib\Net\SSH2 as SSH;
use phpseclib\Net\SFTP as Net_SFTP;

class ServersController extends Controller
{

    
    public function index() {
        $servers = Server::all();
        // return $servers;
        return view('admin1.server', compact('servers'));
    }

    public function CreatNewServer() {
        
        // $servers = Server::all();
        return view('admin1.create-server');
    }
    public function CreatNewServer_Post(Request $request) {
        $ssh = New SSH($request->IP, $request->Port);
        if(!$ssh->login($request->username, $request->password)) {
            abort(500);
        }

        // $response = $ssh->exec('apt-get -y install software-properties-common');
        $response = $ssh->exec('apt-get -y autoremove');

        return $response;


        // SFTP connect
        $sftp = new Net_SFTP($request->IP);
        if (!$sftp->login($request->username, $request->password)) {
            exit('bad login');
        }

        // SSH connection
        $ssh = New SSH($request->IP, $request->Port);
        if(!$ssh->login($request->username, $request->password)) {
            abort(500);
        }

        // check user root
        $response = $ssh->exec('(id -u)');
        if ($response==0){
            // return $response;
        }

        // Create App file
        $response = $ssh->exec('mkdir cipi ');
        $response = $ssh->exec('chmod o-r /cipi');

        // Reposatrie
        $response = $ssh->exec('apt-get -y install software-properties-common');
        $response = $ssh->exec('apt-get -y autoremove');
        $response = $ssh->exec('apt-get update');
        $response = $ssh->exec('apt-get upgrade -y');
        // $response = $ssh->exec('apt-get update');

        return $response;




        return $response;

        

        $uploadFile =   $sftp->put(
             'hostadd.sh',
            'scripts/hostadd.sh',
            Net_SFTP::SOURCE_LOCAL_FILE);

            // Use sftp to make an mv
        
        
       

        



        
        // $pass = sha1(uniqid().microtime().$server->ip);
        // $ssh->setTimeout(360);
        // $response = $ssh->exec('echo '.$server->password.' | sudo -S sudo sh /cipi/root.sh -p '.$pass);
 

        // $this->validate($request, [
        //     'name' => 'required',
        //     'ip' => 'required'
        // ]);



        $serv               = new Server();
        $serv->name        = $request->name_server;
        $serv->provider  = $request->name_server;
        $serv->location   = $request->name_server;
        $serv->ip      = $request->IP;
        $serv->port      = $request->Port;
        // $serv->username     = $request->name_server;
        $serv->password     = $request->name_server;
        $serv->dbroot        = $request->name_server;
        $serv->status       = 1;
        $serv->servercode      = 1;
        // $serv->recurring    = $r;
        // $serv->bill_created = $bill_created;
        // $serv->note         = $notes;
        $serv->save();
        // return $serv;
        
        // if($request->ip == $request->server('SERVER_ADDR')) {
        //     $request->session()->flash('alert-error', 'You can\'t install a client server into the same Cipi Server!');
        //     return redirect('/servers');
        // }
        // Server::create([
        //     'name'      => $request->name_server,
        //     'provider'  => $request->name_server,
        //     'location'  => $request->name_server,
        //     'ip'        => $request->IP,
        //     'port'      => 22,
        //     'username'  => $request->username,
        //     'password'  => $request->password,
        //     'dbroot'    => $request->username,
        //     'servercode'=> $request->username
        // ]);
        return redirect('server')->with([
            'message' => 'alert-success', 'Server '.$request->name.' has been created!',
            'message_important' => true
        ]);
        // $request->session()->flash('alert-success', 'Server '.$request->name.' has been created!');
        // return redirect('/servers');
        return "aa";

        return redirect('server')->with([
            'message' => 'alert-success', 'Server '.$request->name.' has been created!',
            'message_important' => true
        ]);

        // return $request;
        // // $dbroot=$request->get("password");
        // $dbroot=$request->get("name_server");
        // $dbroot=$request->get("IP");
        // $dbroot=$request->get("Port");
        // $dbroot=$request->get("username");
        // $dbroot=$request->get("password");
        // $dbroot=$request->get("dbroot");
        // // $servers = Server::all();
        // return view('admin1.create-server');
    }

    public function api() {
        return Server::orderBy('name')->orderBy('ip')->where('status', 2)->get();
    }


    public function get($servercode) {
        $server = Server::where('servercode', $servercode)->with('applications')->firstOrFail();
        return view('server', compact('server'));
    }


    public function create(Request $request) {
        $this->validate($request, [
            'name' => 'required',
            'ip' => 'required'
        ]);
        if($request->ip == $request->server('SERVER_ADDR')) {
            $request->session()->flash('alert-error', 'You can\'t install a client server into the same Cipi Server!');
            return redirect('/servers');
        }
        Server::create([
            'name'      => $request->name,
            'provider'  => $request->provider,
            'location'  => $request->location,
            'ip'        => $request->ip,
            'port'      => 22,
            // 'username'  => 'cipi',
            'password'  => sha1(uniqid().microtime().$request->ip),
            'dbroot'    => sha1(microtime().uniqid().$request->name),
            'servercode'=> sha1(uniqid().$request->name.microtime().$request->ip)
        ]);
        $request->session()->flash('alert-success', 'Server '.$request->name.' has been created!');
        return redirect('/servers');
    }


    public function changeip(Request $request) {
        $this->validate($request, [
            'servercode' => 'required',
            'ip'         => 'required'
        ]);
        $server = Server::where('servercode', $request->servercode)->first();
        if($request->ip == $request->server('SERVER_ADDR')) {
            $request->session()->flash('alert-error', 'You can\'t setup the same Cipi IP!');
            return redirect('/servers');
        }
        $server->ip = $request->input('ip');
        $server->save();
        $request->session()->flash('alert-success', 'The IP of server '.$server->name.' has been updated!');
        return redirect('/servers');
    }


    public function changename(Request $request) {
        $this->validate($request, [
            'servercode' => 'required',
            'name'       => 'required'
        ]);
        $server = Server::where('servercode', $request->servercode)->first();
        $server->name = $request->input('name');
        $server->save();
        $request->session()->flash('alert-success', 'The name of server '.$server->ip.' has been updated!');
        return redirect('/servers');
    }


    public function destroy(Request $request) {
        $this->validate($request, [
            'servercode' => 'required',
        ]);
        $server = Server::where('servercode', $request->servercode)->firstOrFail();
        $server->delete();
        $request->session()->flash('alert-success', 'Server '.$server->name.' has been deleted!');
        return redirect('/servers');
    }


    public function reset($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $pass = sha1(uniqid().microtime().$server->ip);
        $ssh->setTimeout(360);
        $response = $ssh->exec('echo '.$server->password.' | sudo -S sudo sh /cipi/root.sh -p '.$pass);
        if(strpos($response, '###CIPI###') === false) {
            abort(500);
        }
        $response = explode('###CIPI###', $response);
        if(strpos($response[1], 'Ok') === false) {
            abort(500);
        }
        $server->password = $pass;
        $server->save();
        return $pass;
    }

    public function nginx($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo systemctl restart nginx.service');
        return 'OK';
    }

    public function php($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo service php7.4-fpm restart');
        $ssh->exec('sudo service php7.3-fpm restart');
        $ssh->exec('sudo service php7.2-fpm restart');
        return 'OK';
    }

    public function mysql($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo service mysql restart');
        return 'OK';
    }


    public function redis($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('sudo systemctl restart redis.service');
        return 'OK';
    }


    public function supervisor($servercode) {
        $server = Server::where('servercode', $servercode)->where('status', 2)->firstOrFail();
        $ssh = New SSH($server->ip, $server->port);
        if(!$ssh->login($server->username, $server->password)) {
            abort(500);
        }
        $ssh->setTimeout(360);
        $ssh->exec('service supervisor restart');
        return 'OK';
    }

}
