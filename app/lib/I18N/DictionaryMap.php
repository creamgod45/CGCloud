<?php

namespace App\Lib\I18N;

class DictionaryMap
{
    /**
     * @param I18N $i18N
     */
    public function __construct(I18N &$i18N)
    {
        $i = &$i18N;
        // A~Z
        $i->setLanguagev2(ELanguageText::validator_accepted, "The :attribute field must be accepted.");
        $i->setLanguagev2(ELanguageText::validator_accepted_if,
            "The :attribute field must be accepted when :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_active_url, "The :attribute field must be a valid URL.");
        $i->setLanguagev2(ELanguageText::validator_after, "The :attribute field must be a date after :date.");
        $i->setLanguagev2(ELanguageText::validator_after_or_equal,
            "The :attribute field must be a date after or equal to :date.");
        $i->setLanguagev2(ELanguageText::validator_alpha, "The :attribute field must only contain letters.");
        $i->setLanguagev2(ELanguageText::validator_alpha_dash,
            "The :attribute field must only contain letters, numbers, dashes, and underscores.");
        $i->setLanguagev2(ELanguageText::validator_alpha_num,
            "The :attribute field must only contain letters and numbers.");
        $i->setLanguagev2(ELanguageText::validator_array, "The :attribute field must be an array.");
        $i->setLanguagev2(ELanguageText::validator_ascii,
            "The :attribute field must only contain single-byte alphanumeric characters and symbols.");
        $i->setLanguagev2(ELanguageText::validator_before, "The :attribute field must be a date before :date.");
        $i->setLanguagev2(ELanguageText::validator_before_or_equal,
            "The :attribute field must be a date before or equal to :date.");
        $i->setLanguagev2(ELanguageText::validator_between_array,
            "The :attribute field must have between :min and :max items.");
        $i->setLanguagev2(ELanguageText::validator_between_file,
            "The :attribute field must be between :min and :max kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_between_numeric,
            "The :attribute field must be between :min and :max.");
        $i->setLanguagev2(ELanguageText::validator_between_string,
            "The :attribute field must be between :min and :max characters.");
        $i->setLanguagev2(ELanguageText::validator_boolean, "The :attribute field must be true or false.");
        $i->setLanguagev2(ELanguageText::validator_can, "The :attribute field contains an unauthorized value.");
        $i->setLanguagev2(ELanguageText::validator_confirmed, "The :attribute and %other_field% does not match.");
        $i->setLanguagev2(ELanguageText::validator_current_password, "The password is incorrect.");
        $i->setLanguagev2(ELanguageText::validator_date, "The :attribute field must be a valid date.");
        $i->setLanguagev2(ELanguageText::validator_date_equals, "The :attribute field must be a date equal to :date.");
        $i->setLanguagev2(ELanguageText::validator_date_format, "The :attribute field must match the format :format.");
        $i->setLanguagev2(ELanguageText::validator_decimal, "The :attribute field must have :decimal decimal places.");
        $i->setLanguagev2(ELanguageText::validator_declined, "The :attribute field must be declined.");
        $i->setLanguagev2(ELanguageText::validator_declined_if,
            "The :attribute field must be declined when :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_different, "The :attribute field and:other must be different.");
        $i->setLanguagev2(ELanguageText::validator_digits, "The :attribute field must be :digits digits.");
        $i->setLanguagev2(ELanguageText::validator_digits_between,
            "The :attribute field must be between :min and :max digits.");
        $i->setLanguagev2(ELanguageText::validator_dimensions, "The :attribute field has invalid image dimensions.");
        $i->setLanguagev2(ELanguageText::validator_distinct, "The :attribute field has a duplicate value.");
        $i->setLanguagev2(ELanguageText::validator_doesnt_end_with,
            "The :attribute field must not end with one of the following: :values.");
        $i->setLanguagev2(ELanguageText::validator_doesnt_start_with,
            "The :attribute field must not start with one of the following: :values.");
        $i->setLanguagev2(ELanguageText::validator_email, "The :attribute field must be a valid email address.");
        $i->setLanguagev2(ELanguageText::validator_ends_with,
            "The :attribute field must end with one of the following: :values.");
        $i->setLanguagev2(ELanguageText::validator_enum, "The selected :attribute is invalid.");
        $i->setLanguagev2(ELanguageText::validator_exists, "The selected :attribute is invalid.");
        $i->setLanguagev2(ELanguageText::validator_extensions,
            "The :attribute field must have one of the following extensions: :values.");
        $i->setLanguagev2(ELanguageText::validator_field_administrator, "Administrator");
        $i->setLanguagev2(ELanguageText::validator_field_email, "Email");
        $i->setLanguagev2(ELanguageText::validator_field_email_verified_at, "Email Verified Time");
        $i->setLanguagev2(ELanguageText::validator_field_enable, "Enable");
        $i->setLanguagev2(ELanguageText::validator_field_password, "Password");
        $i->setLanguagev2(ELanguageText::validator_field_passwordConfirmed, "Confirm Password");
        $i->setLanguagev2(ELanguageText::validator_field_phone, "Phone");
        $i->setLanguagev2(ELanguageText::validator_field_sendMailVerifyCodeToken, "Mailbox Authentication Token");
        $i->setLanguagev2(ELanguageText::validator_field_token, "Token");
        $i->setLanguagev2(ELanguageText::validator_field_username, "Account");
        $i->setLanguagev2(ELanguageText::validator_field_VerificationCode, "Verification Code");
        $i->setLanguagev2(ELanguageText::validator_file, "The :attribute field must be a file.");
        $i->setLanguagev2(ELanguageText::validator_filled, "The :attribute field must have a value.");
        $i->setLanguagev2(ELanguageText::validator_gt_array, "The :attribute field must have more than :value items.");
        $i->setLanguagev2(ELanguageText::validator_gt_file,
            "The :attribute field must be greater than :value kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_gt_numeric, "The :attribute field must be greater than :value.");
        $i->setLanguagev2(ELanguageText::validator_gt_string,
            "The :attribute field must be greater than :value characters.");
        $i->setLanguagev2(ELanguageText::validator_gte_array, "The :attribute field must have :value items or more.");
        $i->setLanguagev2(ELanguageText::validator_gte_file,
            "The :attribute field must be greater than or equal to :value kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_gte_numeric,
            "The :attribute field must be greater than or equal to :value.");
        $i->setLanguagev2(ELanguageText::validator_gte_string,
            "The :attribute field must be greater than or equal to :value characters.");
        $i->setLanguagev2(ELanguageText::validator_hex_color,
            "The :attribute field must be a valid hexadecimal color.");
        $i->setLanguagev2(ELanguageText::validator_image, "The :attribute field must be an image.");
        $i->setLanguagev2(ELanguageText::validator_in, "The selected :attribute is invalid.");
        $i->setLanguagev2(ELanguageText::validator_in_array, "The :attribute field must exist in :other.");
        $i->setLanguagev2(ELanguageText::validator_integer, "The :attribute field must be an integer.");
        $i->setLanguagev2(ELanguageText::validator_ip, "The :attribute field must be a valid IP address.");
        $i->setLanguagev2(ELanguageText::validator_ipv4, "The :attribute field must be a valid IPv4 address.");
        $i->setLanguagev2(ELanguageText::validator_ipv6, "The :attribute field must be a valid IPv6 address.");
        $i->setLanguagev2(ELanguageText::validator_json, "The :attribute field must be a valid JSON string.");
        $i->setLanguagev2(ELanguageText::validator_lowercase, "The :attribute field must be lowercase.");
        $i->setLanguagev2(ELanguageText::validator_lt_array, "The :attribute field must have less than :value items.");
        $i->setLanguagev2(ELanguageText::validator_lt_file, "The :attribute field must be less than :value kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_lt_numeric, "The :attribute field must be less than :value.");
        $i->setLanguagev2(ELanguageText::validator_lt_string,
            "The :attribute field must be less than :value characters.");
        $i->setLanguagev2(ELanguageText::validator_lte_array,
            "The :attribute field must not have more than :value items.");
        $i->setLanguagev2(ELanguageText::validator_lte_file,
            "The :attribute field must be less than or equal to :value kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_lte_numeric,
            "The :attribute field must be less than or equal to :value.");
        $i->setLanguagev2(ELanguageText::validator_lte_string,
            "The :attribute field must be less than or equal to :value characters.");
        $i->setLanguagev2(ELanguageText::validator_mac_address, "The :attribute field must be a valid MAC address.");
        $i->setLanguagev2(ELanguageText::validator_max, "The :attribute field must be at most :max characters.");
        $i->setLanguagev2(ELanguageText::validator_max_array,
            "The :attribute field must not have more than :max items.");
        $i->setLanguagev2(ELanguageText::validator_max_digits,
            "The :attribute field must not have more than :max digits.");
        $i->setLanguagev2(ELanguageText::validator_max_file,
            "The :attribute field must not be greater than :max kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_max_string,
            "The :attribute field must not be greater than :max characters.");
        $i->setLanguagev2(ELanguageText::validator_mimes, "The :attribute field must be a file of type: :values.");
        $i->setLanguagev2(ELanguageText::validator_mimetypes, "The :attribute field must be a file of type: :values.");
        $i->setLanguagev2(ELanguageText::validator_min, "The :attribute field must be at least :min characters.");
        $i->setLanguagev2(ELanguageText::validator_min_array, "The :attribute field must have at least :min items.");
        $i->setLanguagev2(ELanguageText::validator_min_digits, "The :attribute field must have at least :min digits.");
        $i->setLanguagev2(ELanguageText::validator_min_file, "The :attribute field must be at least :min kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_min_string,
            "The :attribute field must be at least :min characters.");
        $i->setLanguagev2(ELanguageText::validator_missing, "The :attribute field must be missing.");
        $i->setLanguagev2(ELanguageText::validator_missing_if,
            "The :attribute field must be missing when :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_missing_unless,
            "The :attribute field must be missing unless :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_missing_with,
            "The :attribute field must be missing when :values is present.");
        $i->setLanguagev2(ELanguageText::validator_missing_with_all,
            "The :attribute field must be missing when :values are present.");
        $i->setLanguagev2(ELanguageText::validator_multiple_of, "The :attribute field must be a multiple of :value.");
        $i->setLanguagev2(ELanguageText::validator_not_in, "The selected :attribute is invalid.");
        $i->setLanguagev2(ELanguageText::validator_not_regex, "The :attribute field format is invalid.");
        $i->setLanguagev2(ELanguageText::validator_numeric, "The :attribute field must be a number.");
        $i->setLanguagev2(ELanguageText::validator_password_letters,
            "The :attribute field must contain at least one letter.");
        $i->setLanguagev2(ELanguageText::validator_password_mixed,
            "The :attribute field must contain at least one uppercase and one lowercase letter.");
        $i->setLanguagev2(ELanguageText::validator_password_numbers,
            "The :attribute field must contain at least one number.");
        $i->setLanguagev2(ELanguageText::validator_password_symbols,
            "The :attribute field must contain at least one symbol.");
        $i->setLanguagev2(ELanguageText::validator_password_uncompromised,
            "The given :attribute has appeared in a data leak. Please choose a different :attribute.");
        $i->setLanguagev2(ELanguageText::validator_present, "The :attribute field must be present.");
        $i->setLanguagev2(ELanguageText::validator_present_if,
            "The :attribute field must be present when :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_present_unless,
            "The :attribute field must be present unless :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_present_with,
            "The :attribute field must be present when :values is present.");
        $i->setLanguagev2(ELanguageText::validator_present_with_all,
            "The :attribute field must be present when :values are present.");
        $i->setLanguagev2(ELanguageText::validator_prohibited, "The :attribute field is prohibited.");
        $i->setLanguagev2(ELanguageText::validator_prohibited_if,
            "The :attribute field is prohibited when :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_prohibited_unless,
            "The :attribute field is prohibited unless :other is in :values.");
        $i->setLanguagev2(ELanguageText::validator_prohibits,
            "The :attribute field prohibits :other from being present.");
        $i->setLanguagev2(ELanguageText::validator_regex, "The :attribute field format is invalid.");
        $i->setLanguagev2(ELanguageText::validator_required, "The :attribute field is required.");
        $i->setLanguagev2(ELanguageText::validator_required_array_keys,
            "The :attribute field must contain entries for: :values.");
        $i->setLanguagev2(ELanguageText::validator_required_if,
            "The :attribute field is required when :other is :value.");
        $i->setLanguagev2(ELanguageText::validator_required_if_accepted,
            "The :attribute field is required when :other is accepted.");
        $i->setLanguagev2(ELanguageText::validator_required_unless,
            "The :attribute field is required unless :other is in :values.");
        $i->setLanguagev2(ELanguageText::validator_required_with,
            "The :attribute field is required when :values is present.");
        $i->setLanguagev2(ELanguageText::validator_required_with_all,
            "The :attribute field is required when :values are present.");
        $i->setLanguagev2(ELanguageText::validator_required_without,
            "The :attribute field is required when :values is not present.");
        $i->setLanguagev2(ELanguageText::validator_required_without_all,
            "The :attribute field is required when none of :values are present.");
        $i->setLanguagev2(ELanguageText::validator_same, "The :attribute field must match :other.");
        $i->setLanguagev2(ELanguageText::validator_size_array, "The :attribute field must contain :size items.");
        $i->setLanguagev2(ELanguageText::validator_size_file, "The :attribute field must be :size kilobytes.");
        $i->setLanguagev2(ELanguageText::validator_size_numeric, "The :attribute field must be :size ");
        $i->setLanguagev2(ELanguageText::validator_size_string, "The :attribute field must be :size characters.");
        $i->setLanguagev2(ELanguageText::validator_starts_with,
            "The :attribute field must start with one of the following: :values.");
        $i->setLanguagev2(ELanguageText::validator_string, "The :attribute field must be a string.");
        $i->setLanguagev2(ELanguageText::validator_timezone, ":attribute 欄位必須是有效的時區。");
        $i->setLanguagev2(ELanguageText::validator_ulid, ":attribute 欄位必須是有效的ULID。");
        $i->setLanguagev2(ELanguageText::validator_unique, ":attribute 已經被使用了。");
        $i->setLanguagev2(ELanguageText::validator_uploaded, ":attribute 上傳失敗。");
        $i->setLanguagev2(ELanguageText::validator_uppercase, ":attribute 欄位必須是大寫。");
        $i->setLanguagev2(ELanguageText::validator_url, ":attribute 欄位必須是有效的網址。");
        $i->setLanguagev2(ELanguageText::validator_uuid, ":attribute 欄位必須是有效的UUID。");
        $i->setLanguagev2(ELanguageText::pagination_CurrentPage, "現在頁數");
        $i->setLanguagev2(ELanguageText::pagination_firstPage, "首頁");
        $i->setLanguagev2(ELanguageText::pagination_lastPage, "尾頁");
        $i->setLanguagev2(ELanguageText::pagination_next, "下一頁");
        $i->setLanguagev2(ELanguageText::pagination_previous, "上一頁");
        $i->setLanguagev2(ELanguageText::pagination_TotalPages, "總共頁數");
        $i->setLanguagev2(ELanguageText::passwords_reset, "Your password has been reset.");
        $i->setLanguagev2(ELanguageText::passwords_sent, "We have emailed your password reset link.");
        $i->setLanguagev2(ELanguageText::passwords_throttled, "Please wait before retrying.");
        $i->setLanguagev2(ELanguageText::passwords_token, "This password reset token is invalid.");
        $i->setLanguagev2(ELanguageText::passwords_user, "We can't find a user with that email address.");
    }
}
