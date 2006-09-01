#!/usr/bin/perl
#$r=`ifconfig -a |grep -E 'tun[0-9].|inet'`

$ifcfg=`/sbin/ifconfig -a`;

@linhas=split(/\n/,$ifcfg);
$iface="";
$lista=array;
$count=array;
foreach $linha (@linhas) {

   if($linha=~/^[a-z]+[0-9]+: /) {
      my ($interface,$lixo) = split(/:/,$linha);
      #print "Iface: " . $iface . "\n";
      $iface=$interface;
   }

   if($linha=~/inet /) {
      #print $iface . ": " . $linha . "\n";
      if($iface=~/^tun[0-9]+/) {
         @tmp = split(/ \-\-\> /,$linha);
         my($ip,$lixo) = split(/ /,@tmp[1],2);
         #print $iface . ":" .  $ip . "\n";
         # Pegar o ip do tunel
         $lista{$iface}{"ip"} = $ip;
         $count{$ip}++;
      }
   }

   if($linha=~/PID/) {
      if($iface=~/^tun[0-9]+/) {
         my($lixo,$pid) = split(/PID /,$linha);
         $lista{$iface}{"pid"} = $pid;
      }
   }


}

$excluir = array;

# Varre a lista e identifica as interfaces a serem excluidas
foreach $iface (keys %lista) {
   $ip  = $lista{$iface}{"ip"};
   $cnt = $count{$ip};
   if( $cnt gt 1 ) {
      $excluir{$iface} = $lista{$iface};
   }
   #print $iface.":".$ip."/".$cnt."\n";
}

sort($excluir);

# Varre a lista de exclusao
open LOG, ">>/var/log/unlock-pppoe.log";
foreach $iface (keys %excluir) {
   print LOG "Excluir: " . $iface . ": " . $excluir{$iface}{"ip"} . "/" . $excluir{$iface}{"pid"} . "\n";
   kill 9 ,$excluir{$iface}{"pid"};
   system( "/sbin/ifconfig $iface delete");
}
close(LOG);


