import heartbeat from './heartbeat';

heartbeat.ignite(
    function (result) {
        if (result.ok) {
            console.log('Heartbeat sended');
        }
    }
);

var navEnabled = null;

const LOADING = [];

const FRIENDS = [];

const jsonHeader = {
    headers: {
        'Content-Type': 'application-json'
    }
};

var user_profile = null;

const nav_list = {
    friends: {
        enable: async function () {
            if (FRIENDS.length == 0)
            {
                
            } else {

            }
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

document.addEventListener('DOMContentLoaded', async function () {
    await heartbeat.fetchProfile(updateProfile);
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
    const navid = element.getAttribute('navid');
    if (setLoading(navid, true))
    {
        if (navEnabled)
        {
            let navEnabledId = navEnabled.getAttribute('navid');
            navEnabled.classList.remove('nav-enabled');
            nav_list[navEnabledId]['disable']();
            setLoading(navEnabledId, false);
        }
        nav_list[navid]['enable']();
        navEnabled = element;
        element.classList.add('nav-enabled');
    }
}

/**
 * @param {String} id
 * @param {boolean} set
 * @returns {boolean} 
 */
function setLoading(id, set)
{
    if (set)
    {
        if (!isLoading(id))
        {
            LOADING.push(id);
            return true;
        }
    } else if (isLoading(id)) {
        delete LOADING[LOADING.indexOf(id)];
        return true;
    }
    return false;
}

/**
 * @param {String} id
 * @returns {boolean} 
 */
function isLoading(id) 
{
    return LOADING.includes(id);
}

/** @return {void} */
function refresh() {
    window.location.reload();
}

function updateProfile(data) {
    if (user_profile instanceof User)
    {
        User.update(user_profile, data);
    } else {
        user_profile = User.createFromData(data);
    }
}

function getFriendId(id_A, id_B) {
    if (id_A == user_profile.id) {
        return id_B;
    }
    return id_A;
}


class ModelWithId {

    /**
     * @param {String | Number} id 
     */
    constructor (id) {
        this.id = id;
    }

}

class Message extends ModelWithId {

    static inMessageRequest = [];

    /** @var {boolean} */
    isSendedReadRequest = false;

    /**
     * @param {String | Number} id
     * @param {String | Number} sender
     * @param {String | Number} target
     * @param {string} message
     * @param {boolean} read 
     */
    constructor(id, sender, target, message, read) {
        super.constructor(id);
        this.sender = sender;
        this.target = target;
        this.message = message;
        this.read = read;
    }

    async setAsRead() {
        if (this.isSendedReadRequest)
        {
            const params = new URLSearchParams({

            })
            this.isSendedReadRequest = true;
            fetch('/api/message/read' + params, jsonHeader)
            .then(
                async (response) => {
                    const result = await response.text();
                    if (result == '1') {
                        this.read = true;
                    } else {
                        this.isSendedReadRequest = false;
                    }
                }
            ).catch(
                function (error) {
                    this.isSendedReadRequest = false;
                    console.log(error);
                }
            );
        }
    }

    /**
     * @param {number} loaded 
     * @param {number} friendid
     * @return bool
     */
    static async loadRequest(loaded, friendid) {
        if (!Message.inMessageRequest.includes(friendid))
        {
            const params = new URLSearchParams({
                loaded: loaded,
                friend: friendid
            }).toString();
            Message.inMessageRequest.push(friendid);

            const result = await fetch (heartbeat.getMessagesURL + params);
            delete Message.inMessageRequest[Message.inMessageRequest.indexOf(friendid)];
            return result;
        }
    }

    /**
     * @param {Object} data 
     * @returns Message
     */
    fromRequest(data) {
        return new Message(
            data.id,
            data.sender,
            data.number,
            data.message,
            data.read
        );
    }

    /**
     * @param {Element} element 
     */
    addElementIdentifier(element) {
        element.setAttribute('id', 'message-' + String(this.id));
    }

}

class User extends ModelWithId 
{
    
    oldMessagesLoaded = false;

    /**
     * @param {string | number} id 
     * @param {string} name 
     * @param {number} lastheartbeat 
     * @param {boolean} isOnline 
     * @param {string} perfilImage
     * @param {Message[]} messages
     */
    constructor(id, name, lastheartbeat, isOnline, perfilImage, messages = [])
    {
        this.id = id;
        this.name = name;
        this.lastheartbeat = lastheartbeat;
        this.isOnline = isOnline;
        this.perfilImage = perfilImage;
        this.messages = messages;
    }

    /**
     * @param {User} user 
     * @param {Object} data 
     */
    static update(user, data) {
        user.id = data.id;
        user.name = data.name;
        user.lastheartbeat = data.lastheartbeat;
        user.isOnline = data.isOnline;
        user.perfilImage = data.perfil_image;
        user.onUpdate();
    }

    static createFromData(data) {
        return new User(
            data.id,
            data.name,
            data.lastheartbeat,
            data.isOnline,
            data.perfil_image
        );
    }

    onUpdate() {

    }

    async loadMessages() {
        if (!this.oldMessagesLoaded)
        {
            const result = await Message.loadRequest(this.messages.length, this.id);
            const data = await result.json();
            data.messages.forEach(
                (messageSerialized) => {
                    this.messages.push(Message.fromRequest(messageSerialized));
                }
            );
            if (data.resquest_length < data.messages.length) {
                this.oldMessagesLoaded = true;
            } 
            this.onLoadMessages();
        } else {
            return false;
        }
    }

    /** 
     * @param {Element} element 
     */
    addElementIdentifier(element) {
        element.classList.add('user-' + String(this.id));
    }

    onLoadMessages() {

    }

    onChange () {

    }

}
