import heartbeat from './heartbeat';

heartbeat.ignite(
    function (result) {
        if (result.ok) {
            console.log('Heartbeat sended');
        }
    }
);

var navEnabled = null;

var loading = [];

var friends = [];

const nav_list = {
    friends: {
        enable: function () {

        },
        disable: function () {

        }
    },
    requests: {
        enable: function () {

        },
        disable: function () {

        }
    },
    addfriends: {
        enable: function () {

        },
        disable: function () {

        }
    }
};

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.chat-nav-option').forEach(
        function (element) {
            if (navEnabled == null)
            {
                setNav(element);
            }
            if (element.hasAttribute('navid'))
            {
                const navId = element.getAttribute('navid');
                if (navId in nav_list)
                {
                    element.addEventListener('click', function (event) {
                        if (navEnabled == null || navEnabled.getAttribute('navid') != navId)
                        {
                            nav_list[navId]['enable']();
                            setNav(element);
                        } else {
                            event.preventDefault();
                        }
                    })
                } else {
                    throw `Nav id ${navId} does not exist`;
                }
            } else {
                throw 'Element has no attribute navid';
            }
        }
    );
});

function setNav(element)
{
    if (navEnabled)
    {
        navEnabled.classList.remove('nav-enabled');
    }
    navEnabled = element;
    element.classList.add('nav-enabled');
}

function loadFriends() {
    const friends = fetch();
}

