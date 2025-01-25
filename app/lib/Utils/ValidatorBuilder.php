<?php

namespace App\Lib\Utils;

use App\Lib\EShareTableType;
use App\Lib\I18N\ELanguageCode;
use App\Lib\I18N\ELanguageText;
use App\Lib\I18N\I18N;
use App\Rules\JsonString;
use App\Rules\NoIncludeXSSRule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ValidatorBuilder
{
    public EValidatorType $eValidatorType;
    public int $lastkey = 0;
    private I18N $i18N;
    private array $rules;
    private array $customMessages;
    private array $atters;

    public function __construct(I18N $i18N, EValidatorType $eValidatorType = EValidatorType::NULL)
    {
        $this->customValidatorRule();
        $this->eValidatorType = $eValidatorType;
        $this->i18N = $i18N;
        switch ($eValidatorType) {
            case EValidatorType::LOGIN:
                $this->login();
                break;
            case EValidatorType::REGISTER:
                $this->register();
                break;
            case EValidatorType::FORGOTPASSWORD:
                $this->forgotpassword();
                break;
            case EValidatorType::RESETPASSWORD:
                $this->resetpassword();
                break;
            case EValidatorType::RESETPASSWORDPOST:
                $this->resetpasswordpost();
                break;
            case EValidatorType::ANIMALCREATE:
                $this->animalcreate();
                break;
            case EValidatorType::VERIFYCODE:
                $this->verifycode();
                break;
            case EValidatorType::SENDMAILVERIFYCODE:
                $this->sendMailVerifyCode();
                break;
            case EValidatorType::PROFILEGENERAL:
                $this->profilegeneral();
                break;
            case EValidatorType::PROFILEUPDATEEMAIL:
                $this->profileUpdateEmail();
                break;
            case EValidatorType::PROFILEUPDATEPASSWORD:
                $this->profileUpdatePassword();
                break;
            case EValidatorType::NEWMAILVERIFYCODE:
                $this->newMailVerifyCode();
                break;
            case EValidatorType::EMAILVERIFICATION:
                $this->emailVerification();
                break;
            case EValidatorType::Language:
                $this->language();
                break;
            case EValidatorType::GETCLIENTID:
                $this->getClientID();
                break;
            case EValidatorType::SEARCH:
                $this->search();
                break;
            case EValidatorType::IMAGESGEN:
                $this->imageGen();
                break;
            case EValidatorType::SYSTEMSETTINGUPLOAD:
                $this->systemSettingUpload();
                break;
            case EValidatorType::SHARETABLECREATE:
                $this->shareTableCreate();
                break;
            case EValidatorType::SHARETABLEEDIT:
                $this->shareTableEdit();
                break;
            case EValidatorType::NULL:
                break;
        }
    }

    private function customValidatorRule(): void
    {
        Validator::extend('include', function ($attribute, $value, $parameters, $validator) {
            // 假設你要檢查字串是否包含特定的單字
            return str_contains($value, $parameters[0]);
        });
    }

    private function login(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'username' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8'],
            'token' => ['required', 'string'],
        ];
        $this->lastkey = 1;
    }

    private function initMessage(): array
    {
        $arr = [
            'accepted' => 'validator_accepted',
            'accepted_if' => 'validator_accepted_if',
            'active_url' => 'validator_active_url',
            'after' => 'validator_after',
            'after_or_equal' => 'validator_after_or_equal',
            'alpha' => 'validator_alpha',
            'alpha_dash' => 'validator_alpha_dash',
            'alpha_num' => 'validator_alpha_num ',
            'array' => 'validator_array',
            'ascii' => 'validator_ascii',
            'before' => 'validator_before',
            'before_or_equal' => 'validator_before_or_equal',
            'between' => [
                'array' => 'validator_between_array',
                'file' => 'validator_between_file',
                'numeric' => 'validator_between_numeric',
                'string' => 'validator_between_string',
            ],
            'boolean' => 'validator_boolean',
            'can' => 'validator_can',
            'confirmed' => $this->i18N->getLanguage(ELanguageText::validator_confirmed, true)
                ->placeholderParser(
                    "validator_field_passwordConfirmed",
                    $this->i18N->getLanguage(ELanguageText::validator_field_passwordConfirmed),
                )->toString(),
            'current_password' => 'validator_current_password',
            'date' => 'validator_date',
            'date_equals' => 'validator_date_equals',
            'date_format' => 'validator_date_format',
            'decimal' => 'validator_decimal',
            'declined' => 'validator_declined',
            'declined_if' => 'validator_declined_if',
            'different' => 'validator_different',
            'digits' => 'validator_digits',
            'digits_between' => 'validator_digits_between',
            'dimensions' => 'validator_dimensions',
            'distinct' => 'validator_distinct',
            'doesnt_end_with' => 'validator_doesnt_end_with',
            'doesnt_start_with' => 'validator_doesnt_start_with',
            'email' => 'validator_email',
            'ends_with' => 'validator_ends_with',
            'enum' => 'validator_enum',
            'exists' => 'validator_exists',
            'extensions' => 'validator_extensions',
            'file' => 'validator_file',
            'filled' => 'validator_filled',
            'gt' => [
                'array' => 'validator_gt_array',
                'file' => 'validator_gt_file',
                'numeric' => 'validator_gt_numeric',
                'string' => 'validator_gt_string',
            ],
            'gte' => [
                'array' => 'validator_gte_array',
                'file' => 'validator_gte_file',
                'numeric' => 'validator_gte_numeric',
                'string' => 'validator_gte_string',
            ],
            'hex_color' => 'validator_hex_color',
            'image' => 'validator_image',
            'in' => 'validator_in',
            'in_array' => 'validator_in_array',
            'integer' => 'validator_integer',
            'ip' => 'validator_ip',
            'ipv4' => 'validator_ipv4',
            'ipv6' => 'validator_ipv6',
            'json' => 'validator_json',
            'lowercase' => 'validator_lowercase',
            'lt' => [
                'array' => 'validator_lt_array',
                'file' => 'validator_lt_file',
                'numeric' => 'validator_lt_numeric',
                'string' => 'validator_lt_string',
            ],
            'lte' => [
                'array' => 'validator_lte_array',
                'file' => 'validator_lte_file',
                'numeric' => 'validator_lte_numeric',
                'string' => 'validator_lte_string',
            ],
            'mac_address' => 'validator_mac_address',
            'max' => [
                'array' => 'validator_max_array',
                'file' => 'validator_max_file',
                'numeric' => 'validator_max',
                'string' => 'validator_max_string',
            ],
            'max_digits' => 'validator_max_digits',
            'mimes' => 'validator_mimes',
            'mimetypes' => 'validator_mimetypes',
            'min' => [
                'array' => 'validator_min_array',
                'file' => 'validator_min_file',
                'numeric' => 'validator_min',
                'string' => 'validator_min_string',
            ],
            'min_digits' => 'validator_min_digits',
            'missing' => 'validator_missing',
            'missing_if' => 'validator_missing_if',
            'missing_unless' => 'validator_missing_unless',
            'missing_with' => 'validator_missing_with',
            'missing_with_all' => 'validator_missing_with_all',
            'multiple_of' => 'validator_multiple_of',
            'not_in' => 'validator_not_in',
            'not_regex' => 'validator_not_regex',
            'numeric' => 'validator_numeric',
            'password' => [
                'letters' => 'validator_password_letters',
                'mixed' => 'validator_password_mixed',
                'numbers' => 'validator_password_numbers',
                'symbols' => 'validator_password_symbols',
                'uncompromised' => 'validator_password_uncompromised',
            ],
            'present' => 'validator_present',
            'present_if' => 'validator_present_if',
            'present_unless' => 'validator_present_unless',
            'present_with' => 'validator_present_with',
            'present_with_all' => 'validator_present_with_all',
            'prohibited' => 'validator_prohibited',
            'prohibited_if' => 'validator_prohibited_if',
            'prohibited_unless' => 'validator_prohibited_unless',
            'prohibits' => 'validator_prohibits',
            'regex' => 'validator_regex',
            'required' => 'validator_required',
            'required_array_keys' => 'validator_required_array_keys',
            'required_if' => 'validator_required_if',
            'required_if_accepted' => 'validator_required_if_accepted',
            'required_unless' => 'validator_required_unless',
            'required_with' => 'validator_required_with',
            'required_with_all' => 'validator_required_with_all',
            'required_without' => 'validator_required_without',
            'required_without_all' => 'validator_required_without_all',
            'same' => 'validator_same',
            'size' => [
                'array' => 'validator_size_array',
                'file' => 'validator_size_file',
                'numeric' => 'validator_size_numeric',
                'string' => 'validator_size_string',
            ],
            'starts_with' => 'validator_starts_with',
            'string' => 'validator_string',
            'timezone' => 'validator_timezone',
            'unique' => 'validator_unique',
            'uploaded' => 'validator_uploaded',
            'uppercase' => 'validator_uppercase',
            'url' => 'validator_url',
            'ulid' => 'validator_ulid',
            'uuid' => 'validator_uuid',
        ];
        $newarr = [];
        foreach ($arr as $key => $item) {
            if (is_array($item)) {
                foreach ($item as $k => $v) {
                    $isELanguageText = ELanguageText::valueof($v);
                    if ($isELanguageText === null) {
                        $newarr [$key][$k] = $v;
                    } else {
                        $newarr [$key][$k] = $this->i18N->getLanguage($isELanguageText);
                    }
                }
            } else {
                $isELanguageText = ELanguageText::valueof($item);
                if ($isELanguageText === null) {
                    $newarr [$key] = $item;
                } else {
                    $newarr [$key] = $this->i18N->getLanguage($isELanguageText);
                }
            }
        }
        return $newarr;
    }

    private function initAtters(): array
    {
        return [
            'username' => $this->i18N->getLanguage(ELanguageText::validator_field_username),
            'email' => $this->i18N->getLanguage(ELanguageText::validator_field_email),
            'password' => $this->i18N->getLanguage(ELanguageText::validator_field_password),
            'phone' => $this->i18N->getLanguage(ELanguageText::validator_field_phone),
            'token' => $this->i18N->getLanguage(ELanguageText::validator_field_token),
            'verification' => $this->i18N->getLanguage(ELanguageText::validator_field_VerificationCode),
            'sendMailVerifyCodeToken' => $this->i18N->getLanguage(ELanguageText::validator_field_sendMailVerifyCodeToken),
            // "信箱身份驗證權杖"
            'code' => $this->i18N->getLanguage(ELanguageText::validator_field_VerificationCode),
            'lang' => $this->i18N->getLanguage(ELanguageText::language),
            'q' => '搜尋欄位',
        ];
    }

    private function register(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'username' => ['required', 'string', 'max:255', 'unique:members'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:members'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['required', 'string', 'min:10', 'max:255', 'unique:members'],
            'token' => ['required', 'string'],
        ];
        $this->lastkey = 2;
    }

    private function forgotpassword(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'email' => ['required', 'string', 'email', 'max:255'],
            'token' => ['required', 'string'],
        ];
        $this->lastkey = 3;
    }

    private function resetpassword(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'token' => 'required|string',
            'email' => 'required|email',
        ];
        $this->lastkey = 4;
    }

    private function resetpasswordpost(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'token' => 'required|string',
            'token2' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ];
        $this->lastkey = 5;
    }

    private function animalcreate(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'type_id' => ['required', 'numeric', 'max:10', 'unique:animals'],
            'name' => ['required', 'string', 'max:255'],
            'birthday' => ['date', 'nullable'],
            'area' => ['string', 'max:255', 'nullable'],
            'fix' => ['required', 'max:1', 'max_digits:1', 'min_digits:0'],
            'description' => ['string', 'nullable'],
            'personality' => ['string', 'nullable'],
        ];
        $this->lastkey = 6;
    }

    private function verifycode(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'code' => ['required', 'string', 'min:5'],
            'token' => ['required', 'string'],
        ];
        $this->lastkey = 7;
    }

    private function sendMailVerifyCode(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'token' => ['required', 'string'],
        ];
        $this->lastkey = 8;
    }

    private function profilegeneral(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'sendMailVerifyCodeToken' => ['required', 'string'],
            'token' => ['required', 'string'],
            'method' => ['required', 'string'],
        ];
        $this->lastkey = 9;
    }

    private function profileUpdateEmail(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'email' => ['required', 'email', 'max:255'],
            'verification' => ['required', 'string', 'min:5'],
        ];
        $this->lastkey = 10;
    }

    private function profileUpdatePassword(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'current-ps' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed'],
        ];
        $this->lastkey = 11;
    }

    private function newMailVerifyCode(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'token' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255'],
        ];
        $this->lastkey = 12;
    }

    private function emailVerification(): void
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'id' => ['required', 'string'],
            'hash' => ['required', 'string'],
        ];
        $this->lastkey = 13;
    }

    private function language()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $languages = [];
        foreach (ELanguageCode::cases() as $case) {
            $languages [] = $case->name;
        }
        $this->rules = [
            'lang' => ['string', Rule::in($languages)],
        ];
        $this->lastkey = 14;
    }

    private function getClientID()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'ID' => ['string', 'required'],
        ];
        $this->lastkey = 15;
    }

    private function search()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'q' => ['string', new NoIncludeXSSRule],
            'advanced_search' => ['string', new JsonString],
        ];
        $this->lastkey = 16;
    }

    private function imageGen()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'text' => ['string', 'required'],
            'color' => ['hex_color', 'required'],
        ];
        $this->lastkey = 17;
    }

    private function systemSettingUpload()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'ShopImage.*' => 'file|max:1048576', // 1GB = 1048576KB
            'ShopAdPopup.*' => 'file|max:1048576',
            'ShopAdItem.*' => 'file|max:1048576',
        ];
        $this->lastkey = 18;
    }

    private function shareTableCreate()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'token' => ['required', 'string'],
            'shareTableName' => ['required', 'string', 'max:255'],
            'shareTableDescription' => ['string', 'nullable'],
            'shareTableShortCode' => ['string', 'nullable'],
            'shareTableType' => ['required', 'string', Rule::enum(EShareTableType::class)],
            'shareMembers' => ['nullable', 'array', 'exists:members,id'],
            'password' => ['nullable', 'string', 'confirmed'],
            'files' => ['required', 'array', 'exists:virtual_files,uuid']
        ];
        $this->lastkey = 19;
    }

    private function shareTableEdit()
    {
        $this->customMessages = $this->initMessage();
        $this->atters = $this->initAtters();
        $this->rules = [
            'token' => ['required', 'string'],
            'shareTableId' => ['required', 'string', 'max:255'],
            'shareTableName' => ['required', 'string', 'max:255'],
            'shareTableDescription' => ['string', 'nullable'],
            'shareTableType' => ['required', 'string', Rule::enum(EShareTableType::class)],
            'shareMembers' => ['nullable', 'array', 'exists:members,id'],
            'current-password' => ['nullable', 'string'],
            'password' => ['nullable', 'string', 'confirmed'],
            'files' => ['required', 'array', 'exists:virtual_files,uuid']
        ];
        $this->lastkey = 20;
    }

    /**
     * @param       $data
     * @param array $decodeKeyList
     * @param bool  $decodeContext
     *
     * @return array|MessageBag
     * @throws ValidationException
     */
    public function validate($data, array $decodeKeyList = [], bool $decodeContext = false): array|MessageBag
    {
        $newdata = [];
        if ($decodeContext === true) {
            Log::info(var_export($data, true));
            foreach ($data as $key => $datum) {
                if (in_array($key, $decodeKeyList)) {
                    $newdata[$key] = Utilsv2::decodeContext($datum);
                } else {
                    $newdata[$key] = $datum;
                }
            }
            //Log::info("password newdata: ".Json::encode($newdata));
        } else {
            $newdata = $data;
        }
        $validator = Validator::make($newdata, $this->getRules(), $this->getCustomMessages(), $this->getAtters());
        if ($validator->fails()) {
            return $validator->errors();
        }
        return $validator->validate();
    }

    /**
     * @return array
     */
    public function getRules(): array
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getCustomMessages(): array
    {
        return $this->customMessages;
    }

    /**
     * @return array
     */
    public function getAtters(): array
    {
        return $this->atters;
    }

}
