## 容器中访问宿主机

    host.docker.internal 解析为宿主机主机使用的内部 IP 地址
    gateway.docker.internal

## docker-compose 健康检查

    healthcheck:
          test: curl --fail http://localhost:5000/ || exit 1
          interval: 40s
          timeout: 30s
          retries: 3
          start_period: 60s

## 健康检查三个参数

        - AUTOHEAL_INTERVAL=60
        - AUTOHEAL_START_PERIOD=300
        - AUTOHEAL_DEFAULT_STOP_TIMEOUT=10

## docker 健康检查
        # https://docs.docker.com/engine/containers/run/#healthchecks

        docker run --name=test -d \
        --health-cmd='stat /etc/passwd || exit 1' \
        --health-interval=2s \
        busybox sleep 1d

## 容器内操控容器控制命令

    volumes:
      - /var/run/docker.sock:/var/run/docker.sock

## 远程访问容器

    socat -d -d TCP-L:2375,fork UNIX:/var/run/docker.sock

