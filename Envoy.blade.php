@setup
  $user = "axurt";

  $timezone = "Europe/Moscow";

  $path = "/var/www/mdata.tutorib.ru";

  $current = $path . "/current";

  $repo = "git@github.com:IskanderB/LabaDB.git";

  $branch = "master";

  $chmods = [
  'storage/logs'
  ];

  $date = new DateTime('now', new DateTimeZone($timezone));
  $release = $path . '/releases/' . $date->format('YmdTis');
@endsetup

@servers(['production' => $user . "@45.147.177.63"])

@task('clone', ['on' => $on])
  mkdir -p {{ $release }}
  git clone --depth 1 -b {{ $branch }} {{ $repo }} {{ $release }}
  echo "#1 - Repository has been cloned"
@endtask

@task('composer', ['on' => $on])
  composer self-update
  cd {{$release}}
  composer install --no-interaction --no-dev --prefer-dist
  echo "#2 - Composer dependencies have been installed"
@endtask

@task('artisan', ['on' => $on])
  cd {{$release}}

  ln -nfs {{$path}}/.env .env;
  chgrp -h www-data .env;

  php artisan config:clear


  php artisan clear-compiled --env=production;

  echo "#3 - Production dependencies have been installed"
@endtask

@task('chmod', ['on' => $on])
  chgrp -R www-data {{$release}};
  chmod -R ug+rwx {{$release}};

  @foreach($chmods as $file)
    chmod -R 775 {{$release}}/{{$file}}
    chown -R {{$user}}:www-data {{$release}}/{{$file}}

    echo "Permissions have been set for {{$file}}"
  @endforeach

  echo "Permissions have been set"
@endtask

@task("update_symlinks")
  ln -nfs {{$release}} {{$current}};
  chgrp -h www-data {{$current}};

  echo "#5 - Symlink has been set"
@endtask

@macro('deploy', ['on' => 'production'])
clone
composer
artisan
chmod
update_symlinks
@endmacro
