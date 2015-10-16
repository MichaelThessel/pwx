#!/bin/bash
# Updates themes from
# https://bootswatch.com/

themes='cerulean cosmo cyborg darkly flatly journal lumen paper readable sandstone simplex slate spacelab superhero united yeti'

for theme in $themes
do
    echo "Updating bootstrap.${theme}.min.css"
    curl -S  "https://bootswatch.com/${theme}/bootstrap.min.css" > "bootstrap.${theme}.min.css"

    # Wait 2s between updates to prevent request limiting
    sleep 2
done
