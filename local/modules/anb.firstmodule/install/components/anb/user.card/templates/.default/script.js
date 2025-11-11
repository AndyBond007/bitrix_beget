BX.ready(() => {
    // Обработка клика на аватар
    document.querySelectorAll('.js-my-user-card-avatar-show-in-new-page').forEach((item) => {
        BX.Event.bind(item, 'click', (e) => {
            window.open(item.src, '_blank');
        });
    });

    // Обработка клика на текст "Нравится"
    document.querySelectorAll('.js-my-user-card-like').forEach((element) => {
        BX.Event.bind(element, 'click', (e) => {
            const userId = element.dataset.userId;
            
            BX.ajax.runAction(
                'my:module.user.like',
                {
                    data: { likedUserId: userId }
                }
            ).then((response) => {
                if (response.status === 'success') {
					if (response.data.liked)
					{
						element.classList.add('my-user-card__like-text--liked');
					}
					else
					{
						element.classList.remove('my-user-card__like-text--liked');
					}
                }
            }).catch((response) => {
                console.error('Error:', response.errors);
            });
        });
    });
});