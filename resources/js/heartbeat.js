
const chatHeartbeat = {
    ticks: 600,
    is_sended: false,
    ignited: false,
    defaultRequestHeaders: {
        headers: {
            'Content-Type': 'application-json'
        }
    },
    heartbeat: async function (callback = null) {
        if (!this.is_sended)
        {
            this.is_sended = true;
            try {
                const result = await fetch('/heartbeat', this.defaultRequestHeaders);
                if (result.ok)
                {
                    if (callback instanceof Function)
                    {
                        callback(result);
                    }
                }
            } catch (error) {
                console.log(error);
            }
            this.is_sended = false;
            return true;
        }
        return false;
    },
    fetchProfile: async function (callback) {
        fetch('/api/profile', this.defaultRequestBody)
        .then(async (result) => callback(await result.json()))
        .catch(console.log);
    },
    ignite: function (callback = null) {
        if (!this.ignited)
        {
            setInterval(
                function () {
                    chatHeartbeat.heartbeat(callback);
                }, this.ticks
            );
            this.ignited = true;
        }
    },
    /**
     * @param {number} loaded
     * @param {Function} callback 
     */
    fetchFriends: async function (loaded, callback) {
        let headers = structuredClone(this.defaultRequestHeaders);
        headers.method = 'GET';
        
        fetch (this.getFriendsURL + new URLSearchParams({loaded: loaded}).toString(), headers)
        .then (callback)
        .catch(console.log);
    },
    getFriendsURL: '/api/friends',
    getMessagesURL: '/api/message',
}

export default chatHeartbeat;