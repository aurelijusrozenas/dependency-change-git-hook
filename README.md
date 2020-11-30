# Dependency change git hook

Run script when dependency files change after branch checkout/switch or pull or merge.

## Install
In terminal cd to project dir and
```
cd .git/hooks/
git clone https://github.com/emokykla/dependency-change-git-hook.git
ls -s dependency-change-git-hook/post-checkout
chmod +x post-checkout
ls -s dependency-change-git-hook/post-merge
chmod +x post-merge
cp dependency-change-git-hook/post-composer-dependencies-update.sh .
chmod +x post-composer-dependencies-update.sh
cp dependency-change-git-hook/post-yarn-dependencies-update.sh .
chmod +x post-yarn-dependencies-update.sh
```
Edit files to your likings:
- .git/hooks/post-composer-dependencies-update.sh
- .git/hooks/post-yarn-dependencies-update.sh
