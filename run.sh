#!/bin/bash
docker-compose up -d --build
docker exec pornstar_api ansible-playbook -i ansible/inventory.ini ansible/playbook.yml
