git shortlog -s ${1-} |
cut -b8- | # strip the commit counts
sort | uniq
