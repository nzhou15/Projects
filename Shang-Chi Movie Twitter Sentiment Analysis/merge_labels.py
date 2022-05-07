import os
import json
from collections import Counter

annotators = ['linxiao', 'nicole', 'fynnsu']
data_dir = os.path.join(os.path.pardir, '..', 'data')


def extract_annotation_files():
    annotator_data = {}
    for annotator in annotators:
        annotator_file = annotator + '.json'
        with open(os.path.join(data_dir, 'annotation', annotator_file), 'r', encoding="utf-8") as f:
            annotator_data[annotator] = json.load(f)
    return annotator_data


def get_tweet_ids():
    with open(os.path.join(data_dir, 'collection', 'new_tweets.json'), 'r', encoding="utf-8") as f:
        tweet_data = json.load(f)
    tweet_ids = []
    for tweet in tweet_data:
        tweet_ids.append(tweet['id'])
    return tweet_ids


def validate_annotator_data(annotator_data, tweet_ids):
    for annotator in annotators:
        for tweet, id in zip(annotator_data[annotator], tweet_ids):
            if len(tweet['label']) != 2:
                return False
            # this check ensures the tweets are in the same order in all data sets
            if tweet['id'] != id:
                return False
    return True


def get_labels(labels):
    if 'Positive' in labels:
        sentiment = 'Positive'
    elif 'Neutral' in labels:
        sentiment = 'Neutral'
    elif 'Negative' in labels:
        sentiment = 'Negative'
    else:
        print('tweet missing sentiment label')
        return None, None
    labels.remove(sentiment)
    if not sentiment:
        print('tweet missing sentiment label')
    topic = labels[0]
    return sentiment, topic


def get_majority(labels):
    c = Counter(labels)
    best_value, count_best_value = c.most_common()[0]
    if len(c.most_common()) > 1:
        second_best_value, count_second_best_value = c.most_common()[1]
        if count_best_value == count_second_best_value:
            return 'Tie'
    return best_value


def save_json(filepath, data):
    with open(filepath, 'w', encoding='utf-8') as f:
        json.dump(data, f)


def main():
    annotator_data = extract_annotation_files()
    tweet_ids = get_tweet_ids()
    if not validate_annotator_data(annotator_data, tweet_ids):
        print("Some tweet annotations need to be fixed...")

    merged_data = []
    data_to_reconcile = []
    for i in range(len(tweet_ids)):
        sentiments = []
        topics = []
        for annotator in annotators:
            tweet = annotator_data[annotator][i]
            sentiment, topic = get_labels(tweet['label'])
            if not sentiment:
                print(annotator, tweet)  # further validation
            sentiments.append(sentiment)
            topics.append(topic)

        consensus_sentiment = get_majority(sentiments)
        consensus_topic = get_majority(topics)

        tweet.pop('label')
        if consensus_sentiment == 'Tie' or consensus_topic == 'Tie':
            if consensus_sentiment == 'Tie' and consensus_topic != 'Tie':
                tweet['topic'] = consensus_topic
                tweet['sentiment'] = sentiments
            elif consensus_topic == 'Tie' and consensus_sentiment != 'Tie':
                tweet['sentiment'] = consensus_sentiment
                tweet['topic'] = topics
            else:
                tweet['sentiment'] = sentiments
                tweet['topic'] = topics
            data_to_reconcile.append(tweet)
        else:
            tweet['sentiment'] = consensus_sentiment
            tweet['topic'] = consensus_topic
            merged_data.append(tweet)

    # save_json(os.path.join(data_dir, 'annotation', 'incomplete_merged_data.json'), merged_data)
    # save_json(os.path.join(data_dir, 'annotation', 'data_to_reconcile.json'), data_to_reconcile)


if __name__ == '__main__':
    main()