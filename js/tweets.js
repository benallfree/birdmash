jQuery(function($) {
    
    $("#addnewTweet").on("click", addnewTweet);
    
    function addnewTweet() {
        twitterNames = prompt("Please enter a twitter name to display tweets", "");
        // document.cookie = twitterNames;

        $.ajax({
            url: birdmashVars.path,
            type: 'GET',
            dataType: 'json',
            data: {
                namevar: twitterNames
            },
            success: function(result) {
                tweets = "";
                result.map((tweet) => {
                    tweets += `<div class="twitter-containter">${tweet.text}</div><br />`;
                })
                $("#tweets").append(
                    `<div class="twitter-section">
                            <div class="twitter-author">Latest tweets from  ${result[0].user.name} 
                            </div>
                            <br />${tweets} 
                           </div>`
                )
                console.log(name.text);

            },
            error: function(errors) {
                console.log('Request error', errors);
            }

        });

    }
});
