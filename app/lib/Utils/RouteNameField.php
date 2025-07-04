<?php

namespace App\Lib\Utils;

enum RouteNameField: string
{
    // Page
    case PageHome = 'root.Home';
    case PageHome2 = 'root.Home2';
    case PageSystemSettings = 'root.system.settings';
    case PageCustomPage = 'root.system.custom.page';
    case PageCustomPages = 'root.system.custom.pages';
    case PageDesignComponents = 'root.DesignHTMLComponents';
    case PagePasswordReset = 'root.member.password.reset';
    case PagePasswordResetPost = 'root.member.password.reset.post';
    case PageForgetPassword = 'root.member.password.request';
    case PageForgetPasswordPost = 'root.member.password.request.post';
    case PageEmailVerification = 'root.member.email.verification'; //verification.verify
    case PageEmailReSendMailVerification = 'root.member.email.verification.resend';
    case PageProfile = 'root.member.profile';
    case PageProfilePost = 'root.member.profile.post';
    case PageMembers = 'root.member.list';
    case PageLogout = 'root.member.logout';
    case PageRegister = 'root.member.register';
    case PageRegisterPost = 'root.member.register.post';
    case PageLogin = 'root.member.login';
    case PageLoginPost = 'root.member.login.post';
    case PageGetClientID = 'root.ClientID';
    case PageGetClientIDPost = 'root.ClientID.post';
    case PageShopItem = 'root.shop.item';
    case PageShopItemPopover = 'root.shop.item.popover';
    case PageSearchShopItem = 'root.shop.item.search';
    case PageAddShopItemPost = 'root.shop.item.add.post';
    case PageAddShopItem = 'root.shop.item.add';
    case PageImageGenerator = 'root.image.generator';
    case PageShopItemList = 'root.shop.list';
    case PageShopItemEditor = 'root.shop.item.editor';
    case PageShopItemEditorPost = 'root.shop.item.editor.post';
    case PageCustomerOrderListPost = 'root.customer.order.list.post';
    case PageCustomerOrderList = 'root.customer.order.list';
    case PageShareTableItemPost = 'root.sharetable.item.post';
    case PageShareTableItemView = 'root.sharetable.item.view';
    case PageShareTableItemDownload = 'root.sharetable.item.download';
    case PageShareableShareTableItem = 'root.shareable.sharetable.item';
    case PageShareTableItemDelete = 'root.sharetable.item.delete';
    case PageShareTableDelete = 'root.sharetable.delete';
    case PageShareTableItemSuccess = 'root.sharetable.item.success';
    case PagePublicShareTableDownloadItem = 'root.public.sharetable.item.download';
    case PagePublicShareTablePreviewItem = 'root.public.sharetable.item.preview';
    case PagePublicShareTablePreviewFilePlayerDash = 'root.public.sharetable.preview.file.player.dash';
    case PagePreviewFilePlayerDash = 'root.sharetable.preview.file.player.dash';
    case PageMyShareTables = 'root.sharetables.my';

    // API
    case APIEncodeJson = 'root.api.EncodeJson';
    case APILanguage = 'root.api.Language';
    case APIBrowser = 'root.api.Browser';
    case APIBroadcast = 'root.api.Broadcast';
    case APIHTMLTemplateNotification = 'root.api.HTMLTemplateNotification';
    case APIShareTableItemCreatePost = 'root.api.sharetable.item.create.post';
    case APIShareTableItemEditPost = 'root.api.sharetable.item.edit.post';
    case APIShareTableItemUploadImage = 'root.api.shop.item.upload';
    case APIShareTableItemUploadImageRevert = 'root.api.sharetable.item.upload.revert';
    case APIShareTableItemUploadImagePatch = 'root.api.sharetable.item.upload.patch';
    case APIShareTableItemUploadImageFetch = 'root.api.sharetable.item.upload.fetch';
    case APIShareTableItemUploadImageHead = 'root.api.sharetable.item.upload.head';
    case APIShareTableItemList = 'root.api.shop.list';
    case APISystemLogs = 'root.api.system.logs';
    case APISystemSettingUpload = 'root.api.system.upload';
    case APIClientConfig = 'root.api.ClientConfig';
    case APICustomerOrderListPost = 'root.api.customer.order.list.post';
    case APIPreviewFileTemporary = 'root.api.PreviewFileTemporary';
    case APIPreviewFileTemporary2 = 'root.api.PreviewFileTemporary2';
    case APIGetUsers =  'root.api.get.users';
    case APIShareableShareTableItem = 'root.api.shareable.sharetable.item';
    case APIShareTableItemConversion = 'root.api.sharetable.item.conversion';
    case APIPreviewFileDash = 'root.api.PreviewFileDash';
    case APIDashProgress = 'root.api.DashProgress';
    case APIShareTableItemEditPost2 = 'root.api.sharetable.item.edit.post2';
    case APIPublicShareTablePreviewFileDash = 'root.api.public.sharetable.preview.file.dash';
    case APIPreviewFileTemporary4 = 'root.api.PreviewFileTemporary4';
    case APIShareTableAddFile = 'root.api.sharetable.add.file';
}
