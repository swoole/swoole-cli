
git gc --prune=now


git shortlog -s ${1-} |
cut -b8- |
sort | uniq
