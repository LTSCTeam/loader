@echo off
title Installer
echo Installing "requests", "uuid", "wmi". . .
python -m pip install --upgrade pip
pip install requests
pip install uuid
pip install wmi