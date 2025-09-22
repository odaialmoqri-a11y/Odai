<?php
/**
 * SPDX-License-Identifier: MIT
 * (c) 2025 GegoSoft Technologies and GegoK12 Contributors
 */
namespace App\Http\Controllers\Admin\Setting;

use App\Http\Requests\SettingGeneralRequest;
use App\Http\Controllers\Controller;
use App\Traits\SettingProcess;
use Illuminate\Http\Request;
use App\Traits\Common;
use Exception;

class GeneralController extends Controller
{

    use SettingProcess;
    use Common;

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.settings.generalsettings');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SettingGeneralRequest $request)
    {
        try
        {
            $this->updatesettings('sitetitle',$request->sitetitle);
            $this->updatesettings('sitename',$request->sitename);

            if (($request->sitelogo)==null)
            {
                $this->updatesettings('sitelogo',(\config::get('settings.sitelogo')));
            }
            else
            {
                $name= $request->sitelogo->getClientOriginalName();
                $sitelogopath=$this->uploadFile('uploads/settings', $request->sitelogo, $name);
                $this->updatesettings('sitelogo',$sitelogopath);
            }

            if(($request->favicon)==null)
            {
                $this->updatesettings('favicon',(\config::get('settings.favicon')));
            }
            else
            {
                $name= $request->favicon->getClientOriginalName();
                $faviconpath=$this->uploadFile('uploads/settings', $request->favicon,$name);
                $this->updatesettings('favicon',$faviconpath);
            }

            return redirect()->back();
        }
        catch(Exception $e)
        {
            //dd($e->getMessage());
        }
    }
}
