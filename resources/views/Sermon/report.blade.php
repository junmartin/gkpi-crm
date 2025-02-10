<style>
    .row-break{
        border-top: 1px solid black; 
        border-bottom: 0px; 
        border-left: 0px; 
        border-right: 0px;
    }
    .row-reg{
        border: 0px; 
    }
</style>

<table border="1">
    <thead>
        <tr>
            <th>Tanggal</th>
            <th>Kebaktian</th>
            <th>Peserta</th>
        </tr>
    </thead>
    <tbody>
        
        <?php 
            $tgl = "";
            $sermon_id = "";
            foreach ($attds as $atd) { ?>
            
            <?php 
                $class = ($sermon_id != $atd->sermon['id']) ? "row-break" : "row-reg"; ?>
            <tr>
                <td class="<?php echo $class;?>" style="vertical-align:top;">
                    <?php if($tgl != $atd->sermon['sermon_date']){
                        echo $atd->sermon['sermon_date'];
                    }?>
                </td>
                <td class="<?php echo $class;?>">
                    <?php if($sermon_id != $atd->sermon['id']){
                        echo $atd->sermon->ibadah['ibadah_name']." - ".$atd->sermon['ibadah_name'];
                    }?>
                </td>
                <td class="<?php echo $class;?>">
                    <small>
                        <?php echo ($atd->jemaat['nick_name'] != "" ) ? $atd->jemaat['nick_name'] : $atd->jemaat['name'];?>
                    </small>
                </td>
            </tr>
            
        <?php 
            $tgl = $atd->sermon['sermon_date'];
            $sermon_id = $atd->sermon['id'];
            } ?>
    </tbody>
</table>
