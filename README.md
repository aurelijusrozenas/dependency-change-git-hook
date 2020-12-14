# Dependency change git hook

Run script when dependency files change after branch checkout/switch or pull or merge.

## Install
```shell
cd YOUR-PROJECT-PATH
git clone git@github.com:emokykla/dependency-change-git-hook.git .git/hooks/dependency-change-git-hook
composer --working-dir=.git/hooks/dependency-change-git-hook install --no-dev --optimize-autoloader
.git/hooks/dependency-change-git-hook/bin/install
```

## Testing

### Setup
```shell
git clone git@github.com:emokykla/dependency-change-git-hook.git --branch test-post-merge /tmp/git-test
cd /tmp/git-test
git clone git@github.com:emokykla/dependency-change-git-hook.git .git/hooks/dependency-change-git-hook
composer --working-dir=.git/hooks/dependency-change-git-hook install --no-dev --optimize-autoloader
.git/hooks/dependency-change-git-hook/bin/install --no-interaction
git clone git@github.com:emokykla/dependency-change-git-hook.git --branch test-post-merge /tmp/git-test2
```

### Test post merge
```shell
cd /tmp/git-test2
echo -e "\n" >> package.json && echo -e "\n" >> composer.json && git ci -am 'test' && git push
cd /tmp/git-test
git pull
```

### Test post checkout
```shell
cd /tmp/git-test
git checkout test-post-checkout1 && git checkout test-post-checkout2
```

### Cleanup
```shell
cd /tmp
rm -rf /tmp/git-test/ /tmp/git-test2/
```
