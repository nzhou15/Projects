import json

def get_data(filename):
    with open(filename, 'r') as f:
        data = json.load(f)
    return data


def create_new_json(data, filename):
    new_tweets = []
    for x in data:
        tweet_data = {'created_at':x['created_at'], 'id': x['id'], 'text': x['full_text'], 'user': x['user']['screen_name'], 'user_id': x['user']['id'],
        'twitter_url': f"https://twitter.com/{x['user']['screen_name']}/status/{x['id']}",
        'im_url': f'https://comp598finalimagehosting.s3.us-east-2.amazonaws.com/imgs/{x["id"]}.png'}
        new_tweets.append(tweet_data)

    with open(filename, 'w') as f:
        json.dump(new_tweets, f)


def main():
    filename = 'tweets.json'
    data = get_data(filename)
    create_new_json(data, 'new_tweets.json')


if __name__ == '__main__':
    main()