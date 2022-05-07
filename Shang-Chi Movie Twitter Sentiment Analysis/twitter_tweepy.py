import tweepy
import json

def get_keys_from_json(filename):
    with open(filename) as f:
        keys = json.load(f)
    return keys

def get_api(keys):
    auth = tweepy.OAuthHandler(keys['consumer_key'], keys['consumer_secret'])
    auth.set_access_token(keys['access_token'], keys['access_token_secret'])
    api = tweepy.API(auth, wait_on_rate_limit=True)
    return api

def get_recent_tweets_on_topic(api, topic, count):
    tweets = []

    tweet_response = api.search_tweets(f'{topic} -filter:retweets since:{"2021-11-12"} until:{"2021-11-15"}', count=min(count, 100), result_type='recent', lang='en', tweet_mode='extended')
    tweets += [x._json for x in tweet_response]

    while len(tweets) < count:
        tweet_response = api.search_tweets(f'{topic} -filter:retweets since:{"2021-11-12"} until:{"2021-11-15"}', count=min(100, count - len(tweets)), result_type='recent', lang='en', max_id=tweets[-1]['id'] - 1, tweet_mode='extended')
        tweets += [x._json for x in tweet_response]
    
    return tweets



def main():
    keys = get_keys_from_json('twitter_keys.json')
    api = get_api(keys)
    tweets = get_recent_tweets_on_topic(api, 'shang-chi', 1000)

    with open('tweets.json', 'w') as f:
        json.dump(tweets, f)
    

if __name__ == '__main__':
    main()