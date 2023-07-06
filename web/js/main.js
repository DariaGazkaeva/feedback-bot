document.addEventListener('DOMContentLoaded', () => {
    const replyButtons = document.querySelectorAll('.reply-button');
    const seeButtons = document.querySelectorAll('.see-button');
    const modalReply = document.querySelector('.reply_modal');
    const modalSee = document.querySelector('.see_modal');
    const modalReplyClose = document.querySelector('.reply-close');
    const modalSeeClose = document.querySelector('.see-close');
    const replyForm = document.querySelector('.reply-form');
    const modalSeeContent = modalSee.querySelector('.content_modal');

    replyButtons.forEach(button => {
        button.addEventListener('click', (event) => {
            modalReply.classList.add('open-modal');
            const userMessageId = button.parentNode.parentNode.id;
            const htmlText = `<input type='hidden' class='user-message-id' name='user-message-id' value='${userMessageId}'>`;
            replyForm.insertAdjacentHTML('beforeend', htmlText)
        })
    })

    seeButtons.forEach(button => {
        button.addEventListener('click', () => {
            modalSee.classList.add('open-modal');
            const userMessageId = button.parentNode.parentNode.id;
            fetch(`/answers?user-message-id=${userMessageId}`, {
                method: "GET",
            }).then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error();
                }
            }).then(data => {
                console.log(data);
                if (data.length > 0) {
                    data.forEach(message => {
                        const htmlText = `<div class='answer'><div class='message_field'>${message.text}</div><div class='message_field'>${message.date}</div></div>`;
                        modalSeeContent.insertAdjacentHTML('beforeend', htmlText)
                    })
                } else {
                    modalSeeContent.insertAdjacentHTML('beforeend', `<p class="no-answer">No answers yet</p>`)
                }
            }).catch(e => {
                console.log(e)
            })
        })
    })

    modalReplyClose.addEventListener('click', () => {
        modalReply.classList.remove('open-modal');
        replyForm.querySelector('.user-message-id').remove();
    })

    modalSeeClose.addEventListener('click', () => {
        modalSee.classList.remove('open-modal');
        modalSeeContent.querySelectorAll('.answer').forEach(el => el.remove());
        modalSeeContent.querySelectorAll('.no-answer').forEach(el => el.remove());
    })
})
