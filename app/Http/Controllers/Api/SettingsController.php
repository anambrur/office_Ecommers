<?php

namespace App\Http\Controllers\Api;


use App\SM\SM;
use App\Mail\ContactMail;
use App\Model\Common\Page;
use App\Rules\SmCustomEmail;
use Illuminate\Http\Request;
use App\Model\Common\Setting;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;

class SettingsController extends Controller
{
    public function aboutUs()
    {
        $aboutUs = Setting::where('option_name', 'sm_theme_options_about_setting')->first();

        if ($aboutUs) {
            $optionValue = unserialize($aboutUs->option_value);
            if (isset($optionValue['wwr_description'])) {
                $optionValue['wwr_description'] = strip_tags($optionValue['wwr_description']);
            }
            $aboutUs->option_value = $optionValue;
            return response()->json($aboutUs->getAttributes());
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function contactUs(Request $request)
    {
        $this->validate($request, [
            "fullname" => "required|min:3|max:40",
            "email" => ["required", new SmCustomEmail],
            "subject" => "required|min:3|max:100",
            "message" => "required|min:5|max:500"
        ]);
        Mail::to(SM::get_setting_value("email"))
            ->queue(new ContactMail((object) $request->all()));
        return response()->json(['message' => 'Mail successfully send. We will contact you as soon as possible.'], 200);
    }

    public function termsAndConditions()
    {
        $terms = Page::where('menu_title', 'Terms and Conditions')->first();

        if ($terms) {
            return response()->json($terms->getAttributes());
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function privacyPolicy()
    {
        $privacy = Page::where('menu_title', 'Privacy Policy')->first();

        if ($privacy) {
            return response()->json($privacy->getAttributes());
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    public function refundPolicy()
    {
        $refund = Page::where('menu_title', 'Return Policy')->first();

        if ($refund) {
            return response()->json($refund->getAttributes());
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }

    #make a function for help and support
    public function helpAndSupport()
    {
        $mobile = SM::get_setting_value('mobile');
        $email = SM::get_setting_value('email');
        $address = SM::get_setting_value('address');

        if (!is_null($mobile) && !is_null($email) && !is_null($address)) {
            return response()->json([
                'mobile' => $mobile,
                'email' => $email,
                'address' => $address
            ]);
        } else {
            return response()->json(['error' => 'Data not found'], 404);
        }
    }
}
