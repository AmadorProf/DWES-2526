#!/bin/bash
# Carga este archivo con: source sail-alias.sh

# El comando mágico 'sail'
alias sail='[ -f sail ] && bash sail || bash vendor/bin/sail'

# Atajos rápidos
alias sup='sail up -d'
alias sdown='sail stop'
alias art='sail artisan'
alias mig='sail artisan migrate'
alias fresh='sail artisan migrate:fresh --seed'
alias t='sail test'
alias composer='sail composer'
alias npm='sail npm'
