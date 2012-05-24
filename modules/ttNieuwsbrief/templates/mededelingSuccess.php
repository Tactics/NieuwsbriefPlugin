<?php

switch($mededeling)
{
  case 'bevestigen': ?>
  
  <h2>Bevestigen</h2>
  <p>
    <strong>Opgelet!</strong>  Uw inschrijving is nog niet definitief, u ontvangt van ons nog een email met daarin een link.  Pas nadat u via deze link uw
    inschrijving bevestigd heeft, zal u van ons emails beginnen te ontvangen.
  </p>

  <?php
  break;
  
  case 'bevestigd': ?>
  
  <h2>Ingeschreven</h2>
  <p>
    Proficiat !  U bent nu ingeschreven op onze nieuwsbrief !
  </p>

  <?php
  break;

  case 'linkverzonden': ?>
  
  <h2>Verzonden</h2>
  <p>
    U ontvangt binnen enkele ogenblikken een email met daarin de link om uw gegevens aan te passen.
  </p>

  <?php
  break;

  case 'uitgeschreven': ?>
  
  <h2>Uitgeschreven</h2>
  <p>
    U bent uitgeschreven uit onze nieuwsbrief.  Vanaf heden ontvangt u van ons geen publicitaire emails meer.
  </p>
  <?php
  break;

  case 'error': ?>
  
  <h2>Fout</h2>
  <p>
    Er trad een fout op bij het versturen van een e-mail naar uw adres.  Probeer het later opnieuw of contacteer ons met
   onderstaande foutmelding.
  </p>
  <p>
    Onze excuses voor het ongemak.
  </p>
  <p>
    <?php echo $error; ?>
  </p>
  <?php
  break;

  case 'emailnietgevonden': ?>
  
  <h2>E-mailadres onbekend</h2>
  <p>
    Het adres "<?php echo $email; ?>" is bij ons niet bekend.
  </p>
  <p>
    <a href='javascript:history.back();'>Ga terug naar de vorige pagina.</a>
  </p>

  <?php
}
?>
