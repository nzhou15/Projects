import os, sys
import json
import argparse

data_dir = os.path.join(os.path.pardir, '..', 'data')


def parse_args():
    argparser = argparse.ArgumentParser()
    argparser.add_argument('file1', type=str)
    argparser.add_argument('file2', type=str)

    args = argparser.parse_args(sys.argv[1:])
    return args.file1, args.file2


def load_json(filepath):
    with open(filepath, 'r', encoding="utf-8") as f:
        content = json.load(f)
    return content


def main():
    file1, file2 = parse_args()
    content_file1 = load_json(file1)
    content_file2 = load_json(file2)
    # assuming both files are properly formatted (i.e. a list of tweets)
    full_content = content_file1.extend(content_file2)
    with open(os.path.join(data_dir, 'annotation', 'complete_merged_data.json'), 'r', encoding='utf-8') as f:
        json.dump(full_content, f)


if __name__ == '__main__':
    main()
