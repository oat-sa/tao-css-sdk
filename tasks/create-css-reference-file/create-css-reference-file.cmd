@echo off
cls
title CSS SDK: Generate CSS reference file

pushd "%~dp0"

php ./create-css-reference-file.php
pushd "%~dp0"