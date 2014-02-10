#!/bin/bash

# because it sets perms, this should be run as root.

owner="foss4g"
group="foss4g"

localrepo="/home/foss4g/foss4g2014-wordpress"
gitrepo='https://github.com/pdxosgeo/foss4g2014-wordpress.git'

gitbranch='master'
wpdir="/usr/share/wordpress/wp-content"

git=/usr/bin/git
rsync="/usr/bin/rsync -c"

# directories where all content is replaced
overwrite_dirs="themes plugins"
# directories that we copy, but don't delete existing files
save_dirs="uploads"

if [ ! -d ${localrepo} ]; then
  if [ -w `dirname ${localrepo}` ]; then 
    $git clone -q $gitrepo $localrepo
    status=$?
    if [ $status -ne '0' ]; then
      echo "git clone failed in $0"
      echo "removing stray ${localrepo}"
      /bin/rm -rf ${localrepo}
      exit 1
    fi
    #
    # just to be safe
    chown -R ${owner}:${group} ${localrepo}
    chmod 750 $localrepo
  else
    echo "could not greate git clone for ${gitrepo} in $0"
    exit 1
  fi
  
else
  cd $localrepo && $git fetch -q --all > /dev/null 2>&1 && $git reset -q --hard origin/${gitbranch} > /dev/null 2>&1
  status=$?
  if [ $status -ne '0' ]; then
    echo "$0 : fetch from origin failed. bailing"
    exit 1
  fi
fi

for dir in $overwrite_dirs; do
  $rsync -rq --delete ${localrepo}/${dir}/ ${wpdir}/${dir}
  chown -R ${owner}:${group} ${wpdir}/${dir}
  status=$?
  if [ $? -ne "0" ]; then
    echo "rsync failed to sync $dir in repository on `hostname` - please investigate"
  fi
done

for dir in $save_dirs; do
  $rsync -rq ${localrepo}/${dir}/ ${wpdir}/${dir}
  status=$?
  if [ $? -ne "0" ]; then
    echo "rsync failed to sync $dir in repository on `hostname` - please investigate"
  fi
done

/bin/cp  ${localrepo}/sitemap.xml ${wpdir}/../sitemap.xml

# make sure perms on uploads are correct
chown -R apache:${group} ${wpdir}/uploads 
chmod -R ug+w  ${wpdir}/uploads
chmod -R o-w  ${wpdir}/uploads


exit 0
