/**
 * Profile Avatar Update Handler
 */
document.addEventListener('FILEPOND_STORE_SUCCESS', async (e) => {
    const { uuid, context } = e.detail;

    // 只處理頭像上傳的情境
    if (context !== 'MemberProfileAvatar') return;

    console.log('Detected Avatar Upload Success, linking to profile...', uuid);

    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    const profileRoute = document.querySelector('meta[name="profile-post-route"]')?.content || '/profile';

    try {
        const customToken = document.querySelector('input[name="profile_token"]')?.value;
        const response = await fetch(profileRoute, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token, // 原生驗證 (Middleware)
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                method: 'avatar',
                avatar_uuid: uuid,
                token: customToken // 自定義驗證 (Controller 內部)
            })
        });

        const result = await response.json();

        if (response.ok && result.avatar_url) {
            // 更新頁面上的頭像顯示
            const avatarImg = document.querySelector('img[alt="Avatar"]');
            if (avatarImg) {
                avatarImg.src = result.avatar_url;
                
                // 發送通知
                document.dispatchEvent(new CustomEvent("CGTOASTIFY::notice", { 
                    detail: { 
                        message: "大頭照更新成功！", 
                        type: "success" 
                    } 
                }));
            }
        } else {
            console.error('Failed to update avatar:', result.message);
        }
    } catch (err) {
        console.error('Error during avatar linking:', err);
    }
});
