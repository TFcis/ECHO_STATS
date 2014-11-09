ECHO_STATS
=====

Experimental tracking tool for Online Judge solution statistics.
It supports 8 OJ systems now: [TOJ](http://toj.tfcis.org/oj/info/), [ZJ](http://zerojudge.tw/), [UVa](http://uva.onlinejudge.org/), [GJ](http://www.tcgs.tc.edu.tw:1218/), [TIOJ](http://tioj.ck.tp.edu.tw/), [TZJ](http://judge.tnfsh.tn.edu.tw:8080/), [POJ](http://poj.org/), [HOJ](http://hoj.twbbs.org/judge/).

###To install

- Clone the repository onto the local machine. Ensure that the folder is accessible through the internet.
- Manually set read/write permissions to folders ./cache/ and ./config/ to 777.
- Done.

Loading index.php after installation should automatically fetch related data.

###Config Files
You can refer to config/XXX.dat.example
#####groups.dat
```
[Group ID]
[Group Name]
[User TOJ ID csv]
[Problem ID csv (from probs.dat)];
...
```
#####names.dat
```
[User Name] (tab) [TOJ ID] (tab) [UVa ID] (tab) [ZJ account] (tab) [GJ account] (tab) [TIOJ account] (tab) [TZJ account] (tab) [POJ account] (tab) [HOJ account]
...
```
#####probs.dat
```
(Problem 0) [OJ name] (tab) [Problem ID]
(Problem 1) [OJ name] (tab) [Problem ID]
(Problem 2) [OJ name] (tab) [Problem ID]
...
```
If you type space instead of tab in "probs", it will change it to space automatically.
#####inform.dat
```
[Global Information]
<======>
[Group 0 Information (supports html)]
<======>
[Group 1 Information (supports html)]
...
```
