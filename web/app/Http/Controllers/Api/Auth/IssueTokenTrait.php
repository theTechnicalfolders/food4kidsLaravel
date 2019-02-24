<?php

namespace App\Http\Controllers\Api\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

trait IssueTokenTrait{
	public function issueToken(Request $request,$grantType,$scope="*"){

		$params=[
            'grant_type'=>$grantType,
            'client_id'=>$this->client->id,
            'client_secret'=>$this->client->secret,
            'username'=>$request->username?:$request->email,
            'scope'=>$scope

		];
	}
}