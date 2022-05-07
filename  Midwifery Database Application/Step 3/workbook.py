import matplotlib.pyplot as plt

# 2019
count = [2, 3, 1, 1]
month = [1, 5, 11, 12]
plt.bar(month, count)
plt.title('Number of births vs Month (2019)')
plt.xlabel('Month')
plt.xticks(range(0, 13))
plt.ylabel('Count')
plt.ylim(0, 6)
plt.show()

# 2019 - 2020
count = [2, 3, 4, 2, 3, 1]
month = [1, 4, 5, 8, 11, 12]
plt.bar(month, count)
plt.title('Number of births vs Month (2019 - 2020)')
plt.xlabel('Month')
plt.xticks(range(0, 13))
plt.ylabel('Count')
plt.ylim(0, 6)
plt.show()

# 2019 - 2020
count = [2, 3, 5, 1, 2, 1, 4, 1]
month = [1, 4, 5, 7, 8, 9, 11, 12]
plt.bar(month, count)
plt.title('Number of births vs Month (2019 - 2021)')
plt.xlabel('Month')
plt.xticks(range(0, 13))
plt.ylabel('Count')
plt.ylim(0, 6)
plt.show()

# 2019 - 2022
count = [6, 3, 1, 3, 5, 1, 2, 1, 5, 1]
month = [1, 2, 3, 4, 5, 7, 8, 9, 11, 12]
plt.bar(month, count)
plt.title('Number of births vs Month (2019-2022)')
plt.xlabel('Month')
plt.xticks(range(0, 13))
plt.ylabel('Count')
plt.ylim(0, 6)
plt.show()
