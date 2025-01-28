
const chatHeartbeat = {
    ticks: 500,
    is_sended: false,
    ignited: false,
    heartbeat: async function (callback = null) {
        if (!this.is_sended)
        {
            this.is_sended = true;
            try {
                const result = await fetch('/heartbeat', {
                    headers: {
                        'Content-Type': 'application-json'
                    }
                });
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
    getFriends: function (callback = null) {
        return fetch('/api/friends', {
            headers: {
                'Content-Type': 'application-json'
            }
        });
    }
}

export default chatHeartbeat;