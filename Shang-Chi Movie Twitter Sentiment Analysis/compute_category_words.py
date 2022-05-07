import pandas as pd
import math
import json

category = ["Positive", "Neutral", "Negative", "action", "representation", "Cast", "Characters / story arc", "Pop culture", "Disney+", "Watching/Opinion", "Other"]

def get_tweets_by_category(df):
    sentiment = ["Positive", "Neutral", "Negative"]
    topic = ["action", "representation", "Cast", "Characters / story arc", "Pop culture", "Disney+", "Watching/Opinion", "Other"]
    
    tweets = []

    # gets the data of tweets by category
    for s in sentiment:
        tweets.append(df[df['sentiment'] == s]['data'])

    for t in topic:
        tweets.append(df[df['topic'] == t]['data'])

    return tweets

def get_stopwords():
    with open("../../data/analysis/stopwords.txt", 'r') as file:
        for i in range(6):
            next(file)
        return file.read().splitlines()

def get_word_counts(tweets_by_category):
    punctuation = ['(', ')', '[', ']', ',', '-', '.', '?', '!', ':', ';', '#', '&']
    title = ["shang", "chi", "legend", "ten", "rings"]
    stopwords = get_stopwords()

    all_words = []
    all_dict = {}
    for i in range(len(category)):
        tweets = tweets_by_category[i]

        dict = {}
        for tweet in tweets:
            for letter in tweet:
                # replaces punctuation characters with a space
                if letter in punctuation: 
                    tweet = tweet.replace(letter, ' ')

            words = tweet.lower().split()
            for w in words:
                # excludes the stopwords and the words in title
                if w not in stopwords and w.isalpha() and w not in title:
                    if w == "san":
                        print(tweet)
                    
                    if w not in all_dict:
                        all_words.append(w)
                        
                    if w in dict.keys():
                        dict[w] = dict.get(w) + 1
                    else:
                        dict[w] = 1
            all_dict[category[i]] = dict
    
    # deletes words that occur less than 5 times
    delete = []
    for w in all_words:       
        num = 0
        for c in category:
            if all_dict.get(c) is not None:
                if all_dict.get(c).get(w):
                    num += all_dict.get(c).get(w)
        
        if num < 5:
            delete.append(w)
    
    for d in delete:
        for c in category:
            if all_dict.get(c) is not None:
                if all_dict.get(c).get(d):
                    all_dict.get(c).pop(d)
    return all_dict
    
def compute_tf_idf_score(word_counts):
    list = []
    for i in range(len(category)):
        dict = {}

        if word_counts.get(category[i]) is not None:
            for w in word_counts.get(category[i]).keys():
                tf = 0
                # tf(w, catagory) = the number of times the word w is used under catagory
                if word_counts.get(category[i]) is not None and word_counts.get(category[i]).get(w) is not None:
                    tf = word_counts.get(category[i]).get(w) 

                # counts the number of ponies used word w
                num_of_used = 0
                for j in range(len(category)):
                    if word_counts.get(category[j]) is not None and word_counts.get(category[j]).keys() is not None:
                        if w in word_counts.get(category[j]).keys():
                            num_of_used += 1

                # idf idf(w, script) = log [ (total number of catagories) / (number of categories that use the word w) ]
                idf = math.log10(len(category) / num_of_used)

                dict[w] = tf * idf
        
        # sorts the dictionary in descending order
        list.append(sorted(dict.items(), key=lambda x: x[1], reverse=True))
    
    output = {}
    for i in range(len(category)):
        w = []
        # outputs 10 words in each category with the highest tf-idf scores
        for l in list[i][:10]: 
            w.append(l[0])
        output[category[i]] = w

    return output

def main():   
    df = pd.read_json("../../data/annotation/complete_merged_data.json")

    tweets = get_tweets_by_category(df)
    word_counts = get_word_counts(tweets)
    output = compute_tf_idf_score(word_counts)

    with open("../../data/analysis/category_counts.json", 'w') as file:
        json.dump(output, file, indent=4)       



if __name__ == '__main__':
    main()