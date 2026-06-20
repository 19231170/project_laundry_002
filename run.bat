@echo off
setlocal enabledelayedexpansion
title Laravel Dev Helper

:: Robust way to get the Escape character for colors
for /F "tokens=1,2 delims=#" %%a in ('"prompt #$H#$E# & echo on & for %%b in (1) do rem"') do set "ESC=%%b"

set "GREEN=%ESC%[92m"
set "BLUE=%ESC%[94m"
set "RESET=%ESC%[0m"

:menu
cls
echo %GREEN%      ____________________________________________________ %RESET%
echo %GREEN%     /                                                    \ %RESET%
echo %BLUE%    /   $  $  $  $  $  $  $  $  $  $  $  $  $  $  $  $  $   \ %RESET%
echo %GREEN%   ^|                                                      ^| %RESET%
echo %GREEN%   ^|        %BLUE%==================================%GREEN%        ^| %RESET%
echo %GREEN%   ^|              %BLUE%LARAVEL DEVELOPMENT HELPER%GREEN%              ^| %RESET%
echo %GREEN%   ^|        %BLUE%==================================%GREEN%        ^| %RESET%
echo %GREEN%   ^|                                                      ^| %RESET%
echo %BLUE%    \   $  $  $  $  $  $  $  $  $  $  $  $  $  $  $  $  $   / %RESET%
echo %GREEN%     \____________________________________________________/ %RESET%
echo.
echo  %BLUE%[1]%RESET% Start Laravel Server (Artisan)
echo  %BLUE%[2]%RESET% Start Vite (Frontend)
echo  %BLUE%[3]%RESET% Start BOTH (Separate Windows)
echo  %BLUE%[4]%RESET% Open in Browser (%GREEN%http://127.0.0.1:8000%RESET%)
echo  %BLUE%[5]%RESET% Clear Cache (Artisan)
echo  %BLUE%[6]%RESET% Start Laragon
echo  %BLUE%[7]%RESET% Exit
echo.

set "choice="
set /p choice="Choose an option (1-6): "

if "%choice%"=="1" goto serve
if "%choice%"=="2" goto dev
if "%choice%"=="3" goto both
if "%choice%"=="4" goto open
if "%choice%"=="5" goto clear
if "%choice%"=="6" goto laragon
if "%choice%"=="7" goto exit

if not defined choice (
    goto menu
)

echo Invalid choice, try again.
pause
goto menu

:serve
echo Starting Laravel server...
php artisan serve
pause
goto menu

:dev
echo Starting Vite...
npm run dev
pause
goto menu

:both
echo Starting Laravel server in a new window...
start "Laravel Server" cmd /k "php artisan serve"
echo Starting Vite in a new window...
start "Vite Dev" cmd /k "npm run dev"
echo Both servers started!
pause
goto menu

:open
echo Opening browser...
start http://127.0.0.1:8000
goto menu

:clear
echo Clearing cache...
php artisan optimize:clear
pause
goto menu

:exit
echo Goodbye!
timeout /t 2 >nul
exit

:laragon
echo Starting Laragon...
start "" "C:\laragon\laragon.exe"
pause
goto menu
