#!/bin/bash

GITHUB_REF="refs/heads/master"
#GITHUB_REF="refs/tags/1.0.0"
echo "$GITHUB_REF"
x=${GITHUB_REF/refs\/tags\//}
if [[ $x == *"refs"* ]]; then
  x=""
fi
echo "$x"
